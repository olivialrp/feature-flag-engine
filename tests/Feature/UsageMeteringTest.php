<?php

namespace Tests\Feature;

use App\Events\FlagsEvaluated;
use App\Listeners\MeterUsage;
use App\Models\Environment;
use App\Models\Project;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UsageMeteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_evaluation_dispatches_background_event(): void
    {
        Event::fake();

        $tenant = Tenant::create(['name' => 'Acme Corp', 'slug' => 'acme-corp', 'is_active' => true]);
        $project = Project::create(['tenant_id' => $tenant->id, 'name' => 'Web App']);
        $environment = Environment::create(['project_id' => $project->id, 'name' => 'Production']);

        $token = $environment->createToken('sdk-key');

        $this->withToken($token->plainTextToken)->getJson('/api/v1/flags');

        Event::assertDispatched(FlagsEvaluated::class);
    }

    public function test_metering_listener_atomically_upserts_usage_logs(): void
    {
        $tenant = Tenant::create(['name' => 'Acme Corp', 'slug' => 'acme-corp', 'is_active' => true]);
        $project = Project::create(['tenant_id' => $tenant->id, 'name' => 'Web App']);
        $environment = Environment::create(['project_id' => $project->id, 'name' => 'Production']);

        $event = new FlagsEvaluated($environment);
        $listener = new MeterUsage();

        $listener->handle($event);
        $listener->handle($event);
        $listener->handle($event);

        $this->assertDatabaseHas('usage_logs', [
            'tenant_id' => $tenant->id,
            'environment_id' => $environment->id,
            'date' => now()->toDateString(),
            'request_count' => 3,
        ]);
    }
}
