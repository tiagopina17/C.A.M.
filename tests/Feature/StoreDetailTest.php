<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Service;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_detail_page_renders_successfully(): void
    {
        $store = Store::create([
            'name' => 'Mercearia Central de Aveiro',
            'description' => 'Mercearia tradicional com frutas, pão quente e produtos frescos.',
        ]);

        $response = $this->get('/lojas/'.$store->id);

        $response->assertStatus(200);
        $response->assertViewIs('stores.show');
        $response->assertSee('Mercearia Central de Aveiro');
        $response->assertSee('frutas, pão quente e produtos frescos');
        $response->assertSee('Voltar às Lojas');
    }

    public function test_store_detail_page_displays_categories_and_services(): void
    {
        $category = Category::create([
            'name' => 'Padaria & Pastelaria',
            'slug' => 'padaria-pastelaria',
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Pão Rústico de Centeio',
            'slug' => 'pao-rustico-centeio',
        ]);

        $store = Store::create([
            'name' => 'Padaria do Bairro',
            'description' => 'Fornos a lenha desde 1985.',
        ]);

        $store->services()->attach($service->id);

        $response = $this->get('/lojas/'.$store->id);

        $response->assertStatus(200);
        $response->assertSee('Padaria & Pastelaria');
        $response->assertSee('Pão Rústico de Centeio');
    }

    public function test_store_detail_page_displays_catalog_empty_state_when_no_services(): void
    {
        $store = Store::create([
            'name' => 'Loja Nova Sem Artigos',
            'description' => 'Recém-chegada ao bairro.',
        ]);

        $response = $this->get('/lojas/'.$store->id);

        $response->assertStatus(200);
        $response->assertSee('Catálogo em atualização');
        $response->assertSee('Esta mercearia ainda está a abastecer as suas prateleiras digitais.');
    }

    public function test_store_detail_page_returns_404_for_non_existent_store(): void
    {
        $response = $this->get('/lojas/999999');

        $response->assertStatus(404);
    }

    public function test_store_detail_page_renders_map_container_when_coordinates_present(): void
    {
        $store = Store::create([
            'name' => 'Mercearia com Mapa',
            'lat' => 40.64050,
            'lon' => -8.65375,
        ]);

        $response = $this->get('/lojas/'.$store->id);

        $response->assertStatus(200);
        $response->assertSee('id="store-mini-map"', false);
        $response->assertSee('Como Chegar');
    }
}
