<?php

namespace Tests\Feature;

use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_with_stores_and_sections(): void
    {
        Store::create(['name' => 'Mercado Central']);
        Store::create(['name' => 'Padaria Moderna']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertViewHas('stores');
        $response->assertSee('Bem-vindo ao');
        $response->assertSee('S.A.M');
        $response->assertSee('Mercado Central');
        $response->assertSee('Ficha 01');
    }

    public function test_store_search_api_returns_matching_stores(): void
    {
        Store::create(['name' => 'Padaria do Bairro']);
        Store::create(['name' => 'Pastelaria Central']);
        Store::create(['name' => 'Talho São Pedro']);

        $response = $this->getJson('/api/stores/search?q=Pad');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'Padaria do Bairro']);
    }

    public function test_store_search_api_returns_empty_when_no_match(): void
    {
        Store::create(['name' => 'Florista Rosa']);

        $response = $this->getJson('/api/stores/search?q=XYZNonExistent');

        $response->assertStatus(200);
        $response->assertJsonCount(0);
    }
}
