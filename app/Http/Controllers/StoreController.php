<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\View\View;

class StoreController extends Controller
{
    /**
     * Display the specified store profile.
     */
    public function show(Store $store): View
    {
        $store->load(['services.category', 'users']);

        $categories = $store->services
            ->pluck('category')
            ->filter()
            ->unique('id')
            ->values();

        $servicesByCategory = $store->services
            ->groupBy(fn ($service) => $service->category->name ?? 'Geral');

        return view('stores.show', [
            'store' => $store,
            'categories' => $categories,
            'servicesByCategory' => $servicesByCategory,
            'apiKey' => config('services.google.maps_api_key', ''),
            'mapId' => config('services.google.map_id', '4504f8b37365c3d0'),
        ]);
    }
}
