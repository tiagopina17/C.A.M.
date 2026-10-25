<?php

namespace Tests\Feature;

use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class MapControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_map_page_renders_successfully(): void
    {
        $response = $this->get('/mapa');

        $response->assertStatus(200);
        $response->assertViewIs('map');
        $response->assertSee('Mapa de Lojas do Bairro');
    }

    public function test_map_page_passes_stores_with_coordinates_to_view(): void
    {
        Store::create([
            'name' => 'Mercearia Central',
            'description' => 'Mercearia tradicional no centro.',
            'lat' => 38.7118,
            'lon' => -9.1322,
        ]);

        $response = $this->get('/mapa');

        $response->assertStatus(200);
        $response->assertViewHas('stores');
        $response->assertSee('Mercearia Central');
    }

    public function test_map_page_filters_out_stores_without_coordinates(): void
    {
        Store::create([
            'name' => 'Loja Com Coordenadas',
            'lat' => 38.7118,
            'lon' => -9.1322,
        ]);

        Store::create([
            'name' => 'Loja Sem Coordenadas',
            'lat' => null,
            'lon' => null,
        ]);

        $response = $this->get('/mapa');

        $response->assertStatus(200);

        /** @var Collection $stores */
        $stores = $response->viewData('stores');

        $this->assertTrue($stores->contains('name', 'Loja Com Coordenadas'));
        $this->assertFalse($stores->contains('name', 'Loja Sem Coordenadas'));
    }
}
