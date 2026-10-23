<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Service;
use App\Models\Store;
use App\Models\StoreInviteCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_can_have_owners_and_employees(): void
    {
        $owner = User::factory()->create(['name' => 'Owner Person']);
        $employee = User::factory()->create(['name' => 'Employee Person']);

        $store = Store::create([
            'name' => 'Mercado Central',
            'description' => 'A great local market',
            'place_id' => 'ChIJ123',
            'lat' => 41.1579,
            'lon' => -8.6291,
        ]);

        $store->users()->attach($owner->id, ['role' => 'owner']);
        $store->users()->attach($employee->id, ['role' => 'employee']);

        $this->assertCount(1, $store->owners);
        $this->assertEquals('Owner Person', $store->owners->first()->name);

        $this->assertCount(1, $store->employees);
        $this->assertEquals('Employee Person', $store->employees->first()->name);

        $this->assertCount(1, $owner->ownedStores);
        $this->assertEquals('Mercado Central', $owner->ownedStores->first()->name);
    }

    public function test_store_can_have_services_grouped_by_category(): void
    {
        $category = Category::create([
            'name' => 'Alimentação',
            'slug' => 'alimentacao',
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Pão Quente',
            'slug' => 'pao-quente',
        ]);

        $store = Store::create([
            'name' => 'Padaria da Esquina',
        ]);

        $store->services()->attach($service->id);

        $this->assertCount(1, $store->services);
        $this->assertEquals('Pão Quente', $store->services->first()->name);
        $this->assertEquals('Alimentação', $store->services->first()->category->name);
    }

    public function test_store_can_generate_and_validate_invite_codes(): void
    {
        $store = Store::create([
            'name' => 'Loja de Teste',
        ]);

        $activeCode = StoreInviteCode::create([
            'store_id' => $store->id,
            'code' => 'ABC1234',
            'expires_at' => now()->addDays(2),
        ]);

        $expiredCode = StoreInviteCode::create([
            'store_id' => $store->id,
            'code' => 'EXP1234',
            'expires_at' => now()->subDay(),
        ]);

        $usedCode = StoreInviteCode::create([
            'store_id' => $store->id,
            'code' => 'USED123',
            'used_at' => now(),
        ]);

        $this->assertTrue($activeCode->isValid());
        $this->assertFalse($expiredCode->isValid());
        $this->assertFalse($usedCode->isValid());
    }
}
