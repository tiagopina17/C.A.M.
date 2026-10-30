<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Service;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_non_admin_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this
            ->actingAs($user)
            ->get('/admin/dashboard');

        $response->assertForbidden();
    }

    public function test_store_owner_without_admin_role_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $store = Store::factory()->create();
        $store->users()->attach($user->id, ['role' => 'owner']);

        $response = $this
            ->actingAs($user)
            ->get('/admin/dashboard');

        $response->assertForbidden();
    }

    public function test_admin_user_can_view_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@cadobairro.pt',
            'is_admin' => true,
        ]);

        $category = Category::create([
            'name' => 'Padarias & Pastelarias',
            'slug' => 'padarias-pastelarias',
            'description' => 'Pão quente e doces tradicionais',
        ]);

        $service = Service::create([
            'name' => 'Pão Alentejano',
            'slug' => 'pao-alentejano',
            'category_id' => $category->id,
        ]);

        $store = Store::factory()->create([
            'name' => 'Padaria do Bairro Central',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('Painel de');
        $response->assertSee('Administração');
        $response->assertSee('Acesso Restrito: Administrador');
        $response->assertSee('Super Admin');
        $response->assertSee('Padarias & Pastelarias');
        $response->assertSee('Padaria do Bairro Central');
    }

    public function test_admin_sees_administration_link_in_navbar_dropdown(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $regularUser = User::factory()->create(['is_admin' => false]);

        $adminResponse = $this
            ->actingAs($admin)
            ->get('/explorar');

        $adminResponse->assertOk();
        $adminResponse->assertSee('Administração');
        $adminResponse->assertSee('/admin/dashboard');

        $userResponse = $this
            ->actingAs($regularUser)
            ->get('/explorar');

        $userResponse->assertOk();
        $userResponse->assertDontSee('/admin/dashboard');
    }
}
