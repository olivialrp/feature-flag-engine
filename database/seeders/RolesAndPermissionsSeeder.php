<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'view flags']);
        Permission::firstOrCreate(['name' => 'manage flags']);
        Permission::firstOrCreate(['name' => 'manage API keys']);
        Permission::firstOrCreate(['name' => 'manage billing']);

        $viewer = Role::firstOrCreate(['name' => 'Viewer']);
        $viewer->syncPermissions(['view flags']);

        $developer = Role::firstOrCreate(['name' => 'Developer']);
        $developer->syncPermissions(['view flags', 'manage flags', 'manage API keys']);

        $owner = Role::firstOrCreate(['name' => 'Owner']);
        $owner->syncPermissions(['view flags', 'manage flags', 'manage API keys', 'manage billing']);
    }
}
