<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InfoPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_help_page_renders_successfully(): void
    {
        $response = $this->get('/ajuda');

        $response->assertStatus(200);
        $response->assertViewIs('help');
        $response->assertSee('Perguntas');
        $response->assertSee('Frequentes');
        $response->assertSee('Como descubro lojas');
        $response->assertSee('Como posso registar a minha loja');
        $response->assertSee('código de convite');
    }

    public function test_contact_page_renders_successfully(): void
    {
        $response = $this->get('/contacto');

        $response->assertStatus(200);
        $response->assertViewIs('contact');
        $response->assertSee('Fale');
        $response->assertSee('Connosco');
        $response->assertSee('suporte@cadobairro.pt');
        $response->assertSee('Enviar Mensagem');
    }

    public function test_contact_form_submits_successfully_with_valid_data(): void
    {
        $response = $this->post('/contacto', [
            'name' => 'Manuel Ferreira',
            'email' => 'manuel@exemplo.pt',
            'subject' => 'Registo de Loja',
            'message' => 'Gostaria de saber como registar a minha padaria no Cá do Bairro.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $response = $this->post('/contacto', [
            'name' => '',
            'email' => 'not-an-email',
            'subject' => '',
            'message' => 'short',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }
}
