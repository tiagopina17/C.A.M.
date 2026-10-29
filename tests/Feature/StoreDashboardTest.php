<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/gestao/lojas');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_store_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'Tiago Pina',
            'email' => 'tiago@example.com',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/gestao/lojas');

        $response->assertOk();
        $response->assertSee('Gestão de');
        $response->assertSee('Lojas');
        $response->assertSee('Total de Lojas');
        $response->assertSee('Tiago Pina');
    }

    public function test_dashboard_displays_empty_state_when_user_has_no_stores(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/gestao/lojas');

        $response->assertOk();
        $response->assertSee('Ainda não tens nenhuma loja associada');
        $response->assertSee('Registar a Minha Primeira Loja');
    }

    public function test_dashboard_displays_owned_stores_with_owner_badge(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create([
            'name' => 'Padaria Central do Bairro',
            'description' => 'Pão quente a toda a hora.',
        ]);

        $store->users()->attach($user->id, ['role' => 'owner']);

        $response = $this
            ->actingAs($user)
            ->get('/gestao/lojas');

        $response->assertOk();
        $response->assertSee('Padaria Central do Bairro');
        $response->assertSee('Pão quente a toda a hora.');
        $response->assertSee('Proprietário');
        $response->assertSee('Gerir');
        $response->assertSee('Ver Loja');
    }

    public function test_dashboard_displays_employed_stores_with_employee_badge(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create([
            'name' => 'Frutaria da Esquina',
            'description' => 'Fruta fresca todos os dias.',
        ]);

        $store->users()->attach($user->id, ['role' => 'employee']);

        $response = $this
            ->actingAs($user)
            ->get('/gestao/lojas');

        $response->assertOk();
        $response->assertSee('Frutaria da Esquina');
        $response->assertSee('Funcionário');
        $response->assertDontSee('/gestao/lojas/' . $store->id); // Only owners have management link
    }
}
