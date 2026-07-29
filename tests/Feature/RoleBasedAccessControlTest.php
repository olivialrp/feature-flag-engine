<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_assigned_owner_role_and_inherit_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $tenant = Tenant::create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $user->assignRole('Owner');

        $this->assertTrue($user->hasRole('Owner'));
        $this->assertTrue($user->hasPermissionTo('manage billing'));
        $this->assertFalse($user->hasRole('Viewer'));
    }
}
