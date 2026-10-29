<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreInviteCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StoreManagementController extends Controller
{
    /**
     * Display a listing of stores managed by the authenticated user.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $stores = $user->stores()
            ->withCount('users')
            ->with(['services.category'])
            ->orderBy('name', 'asc')
            ->get();

        $stats = [
            'total' => $stores->count(),
            'owned' => $user->ownedStores()->count(),
            'employed' => $user->employedStores()->count(),
        ];

        return view('management.stores.index', compact('stores', 'stats', 'user'));
    }

    /**
     * Show the form for editing the specified store.
     */
    public function edit(Request $request, Store $store): View
    {
        $this->authorizeOwner($request->user(), $store);

        $store->load([
            'users' => fn ($query) => $query->orderBy('name', 'asc'),
            'inviteCodes' => fn ($query) => $query->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->orderBy('created_at', 'desc'),
        ]);

        return view('management.stores.edit', [
            'store' => $store,
            'user' => $request->user(),
            'employees' => $store->users,
            'activeCodes' => $store->inviteCodes,
        ]);
    }

    /**
     * Update the specified store in storage.
     */
    public function update(Request $request, Store $store): RedirectResponse
    {
        $this->authorizeOwner($request->user(), $store);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $store->update($validated);

        return back()->with('success', 'Informações da loja atualizadas com sucesso!');
    }

    /**
     * Remove an employee from the store.
     */
    public function removeEmployee(Request $request, Store $store, User $user): RedirectResponse
    {
        $this->authorizeOwner($request->user(), $store);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Não pode remover-se a si próprio da loja.');
        }

        $store->users()->detach($user->id);

        return back()->with('success', 'Colaborador removido da loja com sucesso!');
    }

    /**
     * Generate a new 7-character invite code for the store.
     */
    public function generateInviteCode(Request $request, Store $store): JsonResponse|RedirectResponse
    {
        $this->authorizeOwner($request->user(), $store);

        do {
            $code = strtoupper(Str::random(7));
        } while (StoreInviteCode::where('code', $code)->exists());

        $invite = $store->inviteCodes()->create([
            'code' => $code,
            'expires_at' => now()->addDays(7),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'code' => $invite->code,
                'expires_at' => $invite->expires_at->format('d/m/Y H:i'),
            ]);
        }

        return back()->with('success', 'Novo código de convite gerado: ' . $invite->code);
    }

    /**
     * Show the page to join a store with an invite code.
     */
    public function showJoinForm(Request $request): View
    {
        $code = strtoupper(trim($request->query('code', '')));

        return view('stores.join', compact('code'));
    }

    /**
     * Join a store using a valid invite code.
     */
    public function join(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:7'],
        ]);

        $code = strtoupper(trim($validated['code']));
        $invite = StoreInviteCode::where('code', $code)->first();

        if (! $invite || ! $invite->isValid()) {
            return back()->with('error', 'Código de convite inválido ou expirado.');
        }

        $user = $request->user();
        $store = $invite->store;

        if ($store->users()->where('users.id', $user->id)->exists()) {
            return back()->with('error', 'Já está associado a esta loja.');
        }

        $store->users()->attach($user->id, ['role' => 'employee']);

        $invite->update([
            'used_by' => $user->id,
            'used_at' => now(),
        ]);

        return redirect()->route('management.stores.index')->with('success', 'Associou-se com sucesso à loja ' . $store->name . '!');
    }

    /**
     * Ensure the user owns the store.
     */
    protected function authorizeOwner(User $user, Store $store): void
    {
        $isOwner = $store->users()
            ->where('users.id', $user->id)
            ->wherePivot('role', 'owner')
            ->exists();

        if (! $isOwner) {
            abort(403, 'Não tem permissão para gerir esta loja.');
        }
    }
}
