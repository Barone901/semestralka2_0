<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure the role exists
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create or update the admin user
        $user = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );

        // Assign role (safe to run multiple times)
        if (! $user->hasRole($adminRole->name)) {
            $user->assignRole($adminRole);
        }
    }
}
