<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'ver usuarios',
            'crear roles',
            'editar roles',
            'eliminar roles',
            'ver roles',
            'crear permisos',
            'editar permisos',
            'eliminar permisos',
            'ver permisos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
