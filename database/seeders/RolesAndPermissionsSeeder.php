<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // products
            'products.view','products.create','products.update','products.delete',

            // media
            'media.upload','media.update','media.delete',

            // designs
            'designs.view','designs.create','designs.update','designs.delete','designs.approve',

            // orders
            'orders.view','orders.update_status','orders.cancel','orders.refund','orders.export',

            // customers
            'customers.view','customers.update','customers.block',

            // admin
            'roles.manage','settings.manage',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $user = Role::firstOrCreate(['name' => 'user']);
        $grafik = Role::firstOrCreate(['name' => 'grafik']);
        $zamestnanec = Role::firstOrCreate(['name' => 'zamestnanec']);
        $admin = Role::firstOrCreate(['name' => 'admin']);

        $user->givePermissionTo([
            'products.view',
        ]);

        $grafik->givePermissionTo([
            'designs.view','designs.create','designs.update','designs.delete',
            'media.upload','media.update',
            'products.view',
        ]);

        $zamestnanec->givePermissionTo([
            'orders.view','orders.update_status','orders.cancel','orders.export',
            'customers.view','customers.update',
            'products.view',
        ]);

        $admin->givePermissionTo(Permission::all());
    }
}
