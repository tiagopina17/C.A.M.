<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class StoreRegistrationController extends Controller
{
    /**
     * Show the 2-step store registration wizard.
     */
    public function create(): View
    {
        $googleMapsApiKey = config('services.google.maps_api_key', '');

        return view('stores.create', compact('googleMapsApiKey'));
    }

    /**
     * Store a newly created store and attach the authenticated user as owner.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'place_id' => ['required', 'string', 'max:255'],
            'place_name' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric'],
            'lon' => ['nullable', 'numeric'],
        ], [
            'name.required' => 'O nome da loja é obrigatório.',
            'name.max' => 'O nome da loja não pode ter mais de 200 caracteres.',
            'place_id.required' => 'A localização da loja é obrigatória.',
        ]);

        // Check if store with same name and location already exists
        $exists = Store::where('name', $validated['name'])
            ->where('place_id', $validated['place_id'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'name' => 'Uma loja com este nome já existe nesta localização.',
            ]);
        }

        $lat = $validated['lat'] ?? null;
        $lon = $validated['lon'] ?? null;

        // If coordinates missing, attempt to fetch from Google Places API or apply fallback
        if (($lat === null || $lon === null) && !empty($validated['place_id'])) {
            $apiKey = config('services.google.maps_api_key', '');
            if (!empty($apiKey)) {
                try {
                    $response = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/place/details/json', [
                        'place_id' => $validated['place_id'],
                        'fields' => 'geometry',
                        'key' => $apiKey,
                    ]);

                    if ($response->successful()) {
                        $location = $response->json('result.geometry.location');
                        if (!empty($location['lat']) && !empty($location['lng'])) {
                            $lat = $location['lat'];
                            $lon = $location['lng'];
                        }
                    }
                } catch (\Throwable $e) {
                    // Fall back below
                }
            }

            // Sensible fallback coordinates if API unavailable or mock place selected
            if ($lat === null || $lon === null) {
                $lat = 38.7223;
                $lon = -9.1393;
            }
        }

        $store = DB::transaction(function () use ($validated, $lat, $lon) {
            $store = Store::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'place_id' => $validated['place_id'],
                'lat' => $lat,
                'lon' => $lon,
            ]);

            $store->users()->attach(Auth::id(), ['role' => 'owner']);

            return $store;
        });

        return redirect()->route('stores.show', $store)
            ->with('status', 'Loja registada com sucesso! Ficou associado como proprietário.');
    }
}

