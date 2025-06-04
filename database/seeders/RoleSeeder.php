<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resources = ['users', 'customers', 'orders', 'memorials', 'categories', 'reviews', 'faqs', 'quotations', 'tags', 'materials'];
        $actions = ['view', 'create', 'edit', 'delete'];

        $allPermissions = [];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $permission = "$action $resource";
                Permission::firstOrCreate(['name' => $permission]);
                $allPermissions[] = $permission;
            }
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($allPermissions);

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $managerPermissions = collect($allPermissions)->filter(
            fn($permission) =>
            !Str::contains($permission, 'delete')
        );
        $manager->syncPermissions($managerPermissions);

        Role::firstOrCreate(['name' => 'customer']);
    }
}
