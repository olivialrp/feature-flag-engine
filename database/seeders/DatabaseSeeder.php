<?php

namespace Database\Seeders;

use App\Models\Environment;
use App\Models\FeatureFlag;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\UsageLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Establish RBAC Foundation
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // 2. Create the Primary Organization (Tenant)
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'acme-corp'],
            ['name' => 'Acme Corporation', 'is_active' => true]
        );

        // 3. Create the Administrator User
        $admin = User::firstOrCreate(
            ['email' => 'johndoe@acme.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'tenant_id' => $tenant->id,
            ]
        );

        if (!$admin->hasRole('Owner')) {
            $admin->assignRole('Owner');
        }

        // 4. Create a Project and Deployment Environments
        $project = Project::firstOrCreate(
            ['name' => 'Enterprise Dashboard', 'tenant_id' => $tenant->id]
        );

        $production = Environment::firstOrCreate(['name' => 'Production', 'project_id' => $project->id]);
        $staging = Environment::firstOrCreate(['name' => 'Staging', 'project_id' => $project->id]);

        // Generate an SDK Token for Staging to display in the terminal
        if ($staging->tokens()->count() === 0) {
            $token = $staging->createToken('staging-sdk-key');
            $this->command->info("\n✅ Seeding Complete!");
            $this->command->info("🧑‍💻 Admin Login: johndoe@acme.com / password");
            $this->command->info("🔑 Staging SDK Token (For API Evaluation): " . $token->plainTextToken . "\n");
        }

        // 5. Populate Feature Flags
        FeatureFlag::firstOrCreate(['environment_id' => $production->id, 'key' => 'new-billing-ui'], ['is_enabled' => false]);
        FeatureFlag::firstOrCreate(['environment_id' => $production->id, 'key' => 'beta-analytics'], ['is_enabled' => true]);

        FeatureFlag::firstOrCreate(['environment_id' => $staging->id, 'key' => 'new-billing-ui'], ['is_enabled' => true]);
        FeatureFlag::firstOrCreate(['environment_id' => $staging->id, 'key' => 'beta-analytics'], ['is_enabled' => true]);

        // 6. Simulate 7 Days of High-Concurrency Usage Logs
        for ($i = 6; $i >= 0; $i--) {
            UsageLog::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'environment_id' => $production->id,
                    'date' => now()->subDays($i)->toDateString(),
                ],
                [
                    'request_count' => rand(15000, 45000), // Simulating heavy enterprise load
                ]
            );
        }
    }
}
