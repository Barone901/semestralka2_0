<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions
        $perms = [
            'manage products',
            'manage categories',
            'manage media',        // bannery/obrázky
            'manage customers',    // upravovať zákazníkov
            'view stats',
            'manage users',        // iba admin
            'manage settings',     // iba admin
            'moderate reviews',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // Roles
        $customer  = Role::firstOrCreate(['name' => 'customer']);
        $designer  = Role::firstOrCreate(['name' => 'designer']);
        $employee  = Role::firstOrCreate(['name' => 'employee']);
        $admin     = Role::firstOrCreate(['name' => 'admin']);

        // Assign permissions
        $designer->syncPermissions(['manage media', 'manage products', 'manage categories']);
        $employee->syncPermissions(['manage products', 'manage categories', 'manage customers', 'view stats', 'moderate reviews']);
        $admin->syncPermissions(Permission::all()); // full access
    }
}

