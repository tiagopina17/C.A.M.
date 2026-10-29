<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}

