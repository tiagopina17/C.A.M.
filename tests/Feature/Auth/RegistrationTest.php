<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Criar');
        $response->assertSee('Conta');
        $response->assertSee('Nome Completo');
        $response->assertSee('Endereço de Email');
        $response->assertSee('Palavra-passe');
        $response->assertSee('Confirmar Palavra-passe');
        $response->assertSee('togglePassword');
        $response->assertSee('togglePasswordConfirm');
    }

    public function test_new_users_can_register_with_strong_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Maria Silva',
            'email' => 'maria@exemplo.pt',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home', absolute: false));
    }

    public function test_registration_fails_when_password_lacks_number(): void
    {
        $response = $this->post('/register', [
            'name' => 'Maria Silva',
            'email' => 'maria@exemplo.pt',
            'password' => 'Password!',
            'password_confirmation' => 'Password!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['password']);
    }

    public function test_registration_fails_when_password_lacks_symbol(): void
    {
        $response = $this->post('/register', [
            'name' => 'Maria Silva',
            'email' => 'maria@exemplo.pt',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['password']);
    }

    public function test_registration_stops_on_first_failure_ignoring_subsequent_errors(): void
    {
        User::factory()->create([
            'email' => 'existente@exemplo.pt',
        ]);

        // Email already exists AND password confirmation does not match.
        // It must stop on first failure and return ONLY the email error.
        $response = $this->post('/register', [
            'name' => 'Novo Nome',
            'email' => 'existente@exemplo.pt',
            'password' => 'Password123!',
            'password_confirmation' => 'MismatchedPassword123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionDoesntHaveErrors(['password']);
    }
}
