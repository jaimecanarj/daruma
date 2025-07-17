<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin = Role::create(['name' => 'admin']);
        $role_user = Role::create(['name' => 'user']);

        $manage_resources = Permission::create(['name' => 'manage_resources']);
        $tracking_mangas = Permission::create(['name' => 'tracking_mangas']);

        $permissions_admin = [$manage_resources];
        $permissions_user = [$tracking_mangas];

        $role_admin->syncPermissions($permissions_admin);
        $role_user->syncPermissions($permissions_user);
    }
}
