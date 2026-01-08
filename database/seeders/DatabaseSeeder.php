<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // 1️⃣ Create Permissions
        $permissions = [
            'dashboard_access',            
            'user_management_access',
            
            // Permission module
            'permission_access',
            'permission_show',
            'permission_create',
            'permission_edit',
            'permission_delete',

            // Role module
            'role_access',
            'role_show',
            'role_create',
            'role_edit',
            'role_delete',

            // User module
            'user_access',
            'user_show',
            'user_create',
            'user_edit',
            'user_delete',

            // Audit Trails module
            'audit_access',
            'audit_show',
            'audit_create',
            'audit_edit',
            'audit_delete',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2️⃣ Create Roles and assign Permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all()); // Spatie method

        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userRole->syncPermissions(['dashboard_access']); // only basic permission

        // 3️⃣ Create Users and assign Roles
        $adminUser = User::factory()->create([
            'name' => 'ADMIN',
            'email' => 'admin@jydz.com',
            'password' => bcrypt('password'), // default password
        ]);
        $adminUser->assignRole($adminRole);

        $regularUser = User::factory()->create([
            'name' => 'John Daniel',
            'email' => 'user@jydz.com',
            'password' => bcrypt('password'),
        ]);
        $regularUser->assignRole($userRole);
    }
}
