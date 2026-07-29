<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_active_tenant_user_can_login_and_receive_token(): void
    {
        $tenant = Tenant::create(['name' => 'Acme Corp', 'slug' => 'acme-corp', 'is_active' => true]);
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'password' => Hash::make('secure-password'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => $user->email,
            'password' => 'secure-password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user' => ['roles', 'permissions']]);
    }

    public function test_suspended_tenant_user_is_blocked_from_login(): void
    {
        $tenant = Tenant::create(['name' => 'Acme Corp', 'slug' => 'acme-corp', 'is_active' => false]);
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'password' => Hash::make('secure-password'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => $user->email,
            'password' => 'secure-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_logout_and_destroy_token(): void
    {
        $tenant = Tenant::create(['name' => 'Acme Corp', 'slug' => 'acme-corp', 'is_active' => true]);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $token = $user->createToken('admin-token')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/admin/logout');

        $response->assertStatus(204);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
