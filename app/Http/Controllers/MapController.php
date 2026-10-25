<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
    /**
     * Display the interactive map with store markers.
     */
    public function index(Request $request): View
    {
        $stores = Store::whereNotNull('lat')
            ->whereNotNull('lon')
            ->with(['services.category'])
            ->get()
            ->map(function ($store) {
                $categories = $store->services->pluck('category')->filter()->unique('id')->pluck('name')->values();

                return [
                    'id' => $store->id,
                    'name' => $store->name,
                    'description' => $store->description ?? '',
                    'lat' => (float) $store->lat,
                    'lon' => (float) $store->lon,
                    'categories' => $categories,
                    'profile_url' => url('/lojas/'.$store->id),
                    'logo_url' => $store->logo_path ? asset('storage/'.$store->logo_path) : null,
                ];
            });

        return view('map', [
            'stores' => $stores,
            'apiKey' => config('services.google.maps_api_key', ''),
            'mapId' => config('services.google.map_id', '4504f8b37365c3d0'),
        ]);
    }
}
