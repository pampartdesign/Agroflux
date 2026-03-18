<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_switch_only_to_their_tenant(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $tenantA = Tenant::query()->create(['name' => 'Tenant A']);
        $tenantB = Tenant::query()->create(['name' => 'Tenant B']);

        $tenantA->users()->attach($user->id);

        $this->actingAs($user)
            ->post(route('tenant.switch'), ['tenant_id' => $tenantA->id])
            ->assertRedirect(route('dashboard'));

        $this->assertSame($tenantA->id, session('current_tenant_id'));

        $this->actingAs($user)
            ->post(route('tenant.switch'), ['tenant_id' => $tenantB->id])
            ->assertForbidden();
    }
}
