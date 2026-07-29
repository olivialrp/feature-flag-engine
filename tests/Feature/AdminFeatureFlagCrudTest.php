<?php

namespace Tests\Feature;

use App\Models\Environment;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFeatureFlagCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_authorized_user_can_create_feature_flag(): void
    {
        $tenant = Tenant::create(['name' => 'Acme Corp', 'slug' => 'acme-corp', 'is_active' => true]);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $user->assignRole('Developer');

        $project = Project::create(['tenant_id' => $tenant->id, 'name' => 'Dashboard']);
        $environment = Environment::create(['project_id' => $project->id, 'name' => 'Production']);

        $response = $this->actingAs($user)->postJson('/api/admin/feature-flags', [
            'environment_id' => $environment->id,
            'key' => 'new-checkout',
            'is_enabled' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['key' => 'new-checkout', 'is_enabled' => true]);
    }

    public function test_unauthorized_user_is_blocked_from_creating_flag(): void
    {
        $tenant = Tenant::create(['name' => 'Acme Corp', 'slug' => 'acme-corp', 'is_active' => true]);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $user->assignRole('Viewer');

        $project = Project::create(['tenant_id' => $tenant->id, 'name' => 'Dashboard']);
        $environment = Environment::create(['project_id' => $project->id, 'name' => 'Production']);

        $response = $this->actingAs($user)->postJson('/api/admin/feature-flags', [
            'environment_id' => $environment->id,
            'key' => 'new-checkout',
            'is_enabled' => true,
        ]);

        $response->assertStatus(403);
    }
}
