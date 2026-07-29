<?php

namespace Tests\Feature;

use App\Models\Environment;
use App\Models\FeatureFlag;
use App\Models\Project;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureFlagEvaluationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $response = $this->getJson('/api/v1/flags');

        $response->assertStatus(401);
    }

    public function test_environment_can_fetch_its_own_feature_flags(): void
    {
        $tenant = Tenant::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);
        $project = Project::create(['tenant_id' => $tenant->id, 'name' => 'Web App']);

        $environment = Environment::create(['project_id' => $project->id, 'name' => 'Production']);
        $token = $environment->createToken('sdk-key');

        FeatureFlag::create(['environment_id' => $environment->id, 'key' => 'new-checkout', 'is_enabled' => true]);
        FeatureFlag::create(['environment_id' => $environment->id, 'key' => 'beta-dashboard', 'is_enabled' => false]);

        $response = $this->withToken($token->plainTextToken)->getJson('/api/v1/flags');

        $response->assertStatus(200)
            ->assertExactJson([
                'environment' => 'Production',
                'flags' => [
                    'new-checkout' => true,
                    'beta-dashboard' => false,
                ],
            ]);
    }
}
