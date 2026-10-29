<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\StoreInviteCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SingleStoreManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_store_edit(): void
    {
        $store = Store::factory()->create();

        $response = $this->get('/gestao/lojas/' . $store->id);

        $response->assertRedirect('/login');
    }

    public function test_non_owner_cannot_access_store_edit(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/gestao/lojas/' . $store->id);

        $response->assertForbidden();
    }

    public function test_employee_cannot_access_store_edit(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create();
        $store->users()->attach($user->id, ['role' => 'employee']);

        $response = $this
            ->actingAs($user)
            ->get('/gestao/lojas/' . $store->id);

        $response->assertForbidden();
    }

    public function test_owner_can_view_store_edit_page(): void
    {
        $owner = User::factory()->create(['name' => 'Comerciante Chefe']);
        $store = Store::factory()->create([
            'name' => 'Mercearia Central',
            'description' => 'Produtos biológicos e locais.',
        ]);
        $store->users()->attach($owner->id, ['role' => 'owner']);

        $response = $this
            ->actingAs($owner)
            ->get('/gestao/lojas/' . $store->id);

        $response->assertOk();
        $response->assertSee('Mercearia Central');
        $response->assertSee('Produtos biológicos e locais.');
        $response->assertSee('Equipa de Colaboradores');
        $response->assertSee('Comerciante Chefe');
    }

    public function test_owner_can_update_store_details(): void
    {
        $owner = User::factory()->create();
        $store = Store::factory()->create([
            'name' => 'Nome Antigo',
            'description' => 'Descricao Antiga',
        ]);
        $store->users()->attach($owner->id, ['role' => 'owner']);

        $response = $this
            ->actingAs($owner)
            ->put('/gestao/lojas/' . $store->id, [
                'name' => 'Nome Novo Atualizado',
                'description' => 'Nova descrição moderna.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('stores', [
            'id' => $store->id,
            'name' => 'Nome Novo Atualizado',
            'description' => 'Nova descrição moderna.',
        ]);
    }

    public function test_owner_can_generate_7_character_invite_code(): void
    {
        $owner = User::factory()->create();
        $store = Store::factory()->create();
        $store->users()->attach($owner->id, ['role' => 'owner']);

        $response = $this
            ->actingAs($owner)
            ->postJson('/gestao/lojas/' . $store->id . '/convites');

        $response->assertOk();
        $response->assertJsonStructure(['success', 'code', 'expires_at']);
        $this->assertTrue($response->json('success'));

        $code = $response->json('code');
        $this->assertSame(7, strlen($code));

        $this->assertDatabaseHas('store_invite_codes', [
            'store_id' => $store->id,
            'code' => $code,
        ]);
    }

    public function test_owner_can_remove_employee(): void
    {
        $owner = User::factory()->create();
        $employee = User::factory()->create(['name' => 'Funcionario Um']);
        $store = Store::factory()->create();

        $store->users()->attach($owner->id, ['role' => 'owner']);
        $store->users()->attach($employee->id, ['role' => 'employee']);

        $this->assertDatabaseHas('store_user', [
            'store_id' => $store->id,
            'user_id' => $employee->id,
        ]);

        $response = $this
            ->actingAs($owner)
            ->delete('/gestao/lojas/' . $store->id . '/colaboradores/' . $employee->id);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('store_user', [
            'store_id' => $store->id,
            'user_id' => $employee->id,
        ]);
    }

    public function test_owner_cannot_remove_themselves(): void
    {
        $owner = User::factory()->create();
        $store = Store::factory()->create();
        $store->users()->attach($owner->id, ['role' => 'owner']);

        $response = $this
            ->actingAs($owner)
            ->delete('/gestao/lojas/' . $store->id . '/colaboradores/' . $owner->id);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('store_user', [
            'store_id' => $store->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_user_can_join_store_using_valid_invite_code(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['name' => 'Loja da Comunidade']);

        $invite = StoreInviteCode::create([
            'store_id' => $store->id,
            'code' => 'ABC1234',
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/lojas/juntar', [
                'code' => 'ABC1234',
            ]);

        $response->assertRedirect('/gestao/lojas');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('store_user', [
            'store_id' => $store->id,
            'user_id' => $user->id,
            'role' => 'employee',
        ]);

        $invite->refresh();
        $this->assertSame($user->id, $invite->used_by);
        $this->assertNotNull($invite->used_at);
    }

    public function test_join_fails_with_invalid_or_expired_code(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create();

        // Expired code
        StoreInviteCode::create([
            'store_id' => $store->id,
            'code' => 'EXPD123',
            'expires_at' => now()->subDay(),
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/lojas/juntar', [
                'code' => 'EXPD123',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('store_user', [
            'store_id' => $store->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_join_store_twice(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create();
        $store->users()->attach($user->id, ['role' => 'employee']);

        StoreInviteCode::create([
            'store_id' => $store->id,
            'code' => 'DUP1234',
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/lojas/juntar', [
                'code' => 'DUP1234',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Já está associado a esta loja.');
    }

    public function test_authenticated_user_can_view_join_store_page(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/lojas/juntar');

        $response->assertOk();
        $response->assertSee('Juntar-se a uma');
        $response->assertSee('Código de Convite');
    }

    public function test_join_store_page_prefills_code_from_query(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/lojas/juntar?code=TEST123');

        $response->assertOk();
        $response->assertSee('TEST123');
    }
}

