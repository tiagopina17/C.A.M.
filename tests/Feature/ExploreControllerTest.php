<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Service;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExploreControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_explore_page_renders_successfully(): void
    {
        $response = $this->get('/explorar');

        $response->assertStatus(200);
        $response->assertViewIs('explore');
        $response->assertSee('Lojas e Mercados do Bairro');
    }

    public function test_explore_page_displays_stores(): void
    {
        Store::create([
            'name' => 'Mercearia do Sr. José',
            'description' => 'Mercearia tradicional com frutas e legumes frescos.',
        ]);

        $response = $this->get('/explorar');

        $response->assertStatus(200);
        $response->assertSee('Mercearia do Sr. José');
        $response->assertSee('frutas e legumes');
    }

    public function test_explore_page_filters_stores_by_search_query(): void
    {
        Store::create(['name' => 'Talho Central']);
        Store::create(['name' => 'Padaria da Esquina']);

        $response = $this->get('/explorar?q=Talho');

        $response->assertStatus(200);
        $response->assertSee('Talho Central');
        $response->assertDontSee('Padaria da Esquina');
    }

    public function test_explore_page_filters_stores_by_category(): void
    {
        $category = Category::create([
            'name' => 'Alimentos',
            'slug' => 'alimentos',
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Produtos Frescos',
            'slug' => 'produtos-frescos',
        ]);

        $matchingStore = Store::create(['name' => 'Frutaria São Pedro']);
        $matchingStore->services()->attach($service->id);

        $otherStore = Store::create(['name' => 'Drogaria Central']);

        $response = $this->get('/explorar?categoria=alimentos');

        $response->assertStatus(200);
        $response->assertSee('Frutaria São Pedro');
        $response->assertDontSee('Drogaria Central');
    }

    public function test_explore_page_shows_empty_state_when_no_stores_found(): void
    {
        $response = $this->get('/explorar?q=Inexistente');

        $response->assertStatus(200);
        $response->assertSee('Nenhuma loja encontrada');
    }
}
