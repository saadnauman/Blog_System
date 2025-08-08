<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Clear cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2️⃣ Define permissions in logical groups
        $permissions = [
            // 📌 Posts
            'post.create',
            'post.view',
            'post.update.own',
            'post.update.any',
            'post.delete.own',
            'post.delete.any',

            // 📌 Categories (admin only)
            'category.create',
            'category.view',
            'category.update',
            'category.delete',

            // 📌 Users
            'user.create',
            'user.view',
            'user.update',
            'user.delete',
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3️⃣ Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // 4️⃣ Assign permissions to roles
        $adminRole->syncPermissions(Permission::all());
        $userRole->syncPermissions([
            'post.create',
            'post.view',
            'post.update.own',
            'post.delete.own',
        ]);
    }
}
