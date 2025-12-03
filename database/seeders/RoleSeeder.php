<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Lista de roles
        $roles = [
            'SECRETARIA',
            'DIRECTOR',
            'FUNCIONARIO',
            'ADMINISTRADOR',
        ];

        foreach ($roles as $roleName) {
            // Crear rol si no existe
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Opcional: asignar permisos básicos a cada rol
        // Ejemplo: si ya tienes permisos definidos
        // $permissions = Permission::all();

        // Role::where('name', 'ADMINISTRADOR')->first()->syncPermissions($permissions);

        $this->command->info('Roles creados correctamente usando Spatie.');
    }
}
