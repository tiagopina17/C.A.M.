<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_store_registration(): void
    {
        $response = $this->get('/lojas/registar');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_store_registration_wizard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/lojas/registar');

        $response->assertStatus(200);
        $response->assertViewIs('stores.create');
        $response->assertSee('Regista a tua');
        $response->assertSee('Loja');
        $response->assertSee('Informações da Loja');
        $response->assertSee('Localização da Loja');
        $response->assertSee('Continuar para Localização');
    }

    public function test_authenticated_user_can_register_store_and_becomes_owner_without_user_deletion(): void
    {
        $user = User::factory()->create([
            'name' => 'António Manuel',
            'email' => 'antonio@bairro.pt',
        ]);

        $response = $this->actingAs($user)->post('/lojas/registar', [
            'name' => 'Padaria Central do Bairro',
            'description' => 'Pão quente a toda a hora com receitas tradicionais.',
            'place_id' => 'ChIJ025f82k0JA0Rk68G6iQ4H_c',
            'place_name' => 'Padaria Central do Bairro, Lisboa',
            'lat' => 38.7169,
            'lon' => -9.1399,
        ]);

        // User must NOT be deleted
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'antonio@bairro.pt',
        ]);
        $this->assertAuthenticatedAs($user);

        // Store must be created
        $store = Store::where('name', 'Padaria Central do Bairro')->first();
        $this->assertNotNull($store);
        $this->assertEquals('ChIJ025f82k0JA0Rk68G6iQ4H_c', $store->place_id);
        $this->assertEquals(38.7169, $store->lat);
        $this->assertEquals(-9.1399, $store->lon);

        // User must be attached as owner in store_user pivot
        $this->assertDatabaseHas('store_user', [
            'store_id' => $store->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        // Relations verify
        $this->assertTrue($user->ownedStores()->where('stores.id', $store->id)->exists());

        // Redirect to store page
        $response->assertRedirect(route('stores.show', $store));
        $response->assertSessionHas('status');
    }

    public function test_store_registration_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/lojas/registar', [
            'name' => '',
            'place_id' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'place_id']);
    }

    public function test_duplicate_store_at_same_location_is_rejected(): void
    {
        $user = User::factory()->create();

        Store::create([
            'name' => 'Mercearia Duplicada',
            'place_id' => 'ChIJ_SAME_LOCATION_123',
            'lat' => 38.71,
            'lon' => -9.13,
        ]);

        $response = $this->actingAs($user)->post('/lojas/registar', [
            'name' => 'Mercearia Duplicada',
            'place_id' => 'ChIJ_SAME_LOCATION_123',
        ]);

        $response->assertSessionHasErrors(['name' => 'Uma loja com este nome já existe nesta localização.']);
    }
}

