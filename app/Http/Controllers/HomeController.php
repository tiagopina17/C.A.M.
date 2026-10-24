<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page with stores list for search.
     */
    public function index(): View
    {
        $stores = Store::query()
            ->select(['id', 'name', 'logo_path'])
            ->orderBy('name')
            ->get();

        return view('home', compact('stores'));
    }

    /**
     * Search stores via JSON API.
     */
    public function search(Request $request): JsonResponse
    {
        $query = (string) $request->input('q', '');

        $stores = Store::query()
            ->when($query !== '', function ($q) use ($query): void {
                $q->where('name', 'like', "{$query}%");
            })
            ->select(['id', 'name', 'logo_path'])
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json($stores);
    }
}
