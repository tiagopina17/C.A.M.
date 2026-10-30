<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_404_error_page_renders_with_market_design_system(): void
    {
        $response = $this->get('/non-existent-route-for-testing-404');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Página');
        $response->assertSee('Não Encontrada');
        $response->assertSee('Explorar Lojas');
        $response->assertSee('Voltar ao Início');
    }

    public function test_403_error_page_renders_when_forbidden(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
        $response->assertSee('403');
        $response->assertSee('Acesso');
        $response->assertSee('Negado');
        $response->assertSee('Voltar ao Início');
    }

    public function test_500_error_page_renders_with_market_design_system(): void
    {
        $response = $this->get('/teste-500');

        $response->assertStatus(500);
        $response->assertSee('500');
        $response->assertSee('Erro');
        $response->assertSee('de Servidor');
        $response->assertSee('Centro de Ajuda');
        $response->assertSee('Fale Connosco');
    }
}

