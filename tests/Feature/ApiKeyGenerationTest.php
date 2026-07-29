<?php

namespace Tests\Feature;

use App\Models\Environment;
use App\Models\Project;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiKeyGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_environment_can_generate_sanctum_api_key(): void
    {
        $tenant = Tenant::create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
            'is_active' => true,
        ]);

        $project = Project::create([
            'tenant_id' => $tenant->id,
            'name' => 'Mobile App',
        ]);

        $environment = Environment::create([
            'project_id' => $project->id,
            'name' => 'Production',
        ]);

        $token = $environment->createToken('production-sdk-key');

        $this->assertNotEmpty($token->plainTextToken);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => Environment::class,
            'tokenable_id' => $environment->id,
            'name' => 'production-sdk-key',
        ]);
    }
}
