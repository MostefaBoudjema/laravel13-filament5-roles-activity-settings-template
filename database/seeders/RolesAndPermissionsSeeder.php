<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create permissions for each model
        $models = [ 'user', 'role', 'permission', 'activity_log', 'academic_year', 'setting'];
        $actions = ['view_any', 'view', 'create', 'update', 'delete'];

        $allPermissions = [];
        foreach ($models as $model) {
            foreach ($actions as $action) {
                $permName = "{$action}_{$model}";
                $allPermissions[] = $permName;
                Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            }
        }

        // Create page-level permissions
        $pagePermissions = [
            'view_page_settings',
            'view_page_stats_dashboard',
        ];

        foreach ($pagePermissions as $perm) {
            $allPermissions[] = $perm;
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);

        // Super-admin gets all permissions
        $superAdmin->syncPermissions($allPermissions);


        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'admin',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('super-admin');

    }
}
