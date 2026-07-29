<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureActiveTenant;
use App\Models\Environment;
use App\Models\FeatureFlag;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_inactive_tenants_are_blocked_by_middleware(): void
    {
        $tenant = Tenant::create(['name' => 'Suspended Corp', 'slug' => 'suspended-corp', 'is_active' => false]);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $request = Request::create('/_test/middleware', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new EnsureActiveTenant();

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Unauthorized: Your organization account is currently suspended.');

        $middleware->handle($request, fn () => response('ok'));
    }

    public function test_users_cannot_update_flags_belonging_to_other_tenants(): void
    {
        $tenantA = Tenant::create(['name' => 'Tenant A', 'slug' => 'tenant-a', 'is_active' => true]);
        $userA = User::factory()->create(['tenant_id' => $tenantA->id]);
        $userA->assignRole('Developer');

        $tenantB = Tenant::create(['name' => 'Tenant B', 'slug' => 'tenant-b', 'is_active' => true]);
        $projectB = Project::create(['tenant_id' => $tenantB->id, 'name' => 'App B']);
        $envB = Environment::create(['project_id' => $projectB->id, 'name' => 'Prod B']);
        $flagB = FeatureFlag::create(['environment_id' => $envB->id, 'key' => 'beta-feature', 'is_enabled' => true]);

        $policy = new \App\Policies\FeatureFlagPolicy();

        $this->assertFalse($policy->update($userA, $flagB));
    }
}
