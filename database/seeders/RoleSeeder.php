<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        Permission::truncate();
        Role::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $permissions = [

            'admin.unidades',
            'admin.unidades.create',
            'admin.unidades.edit',
            'admin.unidades.destroy',
            'admin.unidades.data',
            'admin.unidades.organigrama',
            'admin.unidad.organigrama',

            'admin.funcionarios',
            'admin.funcionarios.create',
            'admin.funcionarios.edit',
            'admin.funcionarios.destroy',
            'admin.funcionarios.data',

            'admin.usuarios',
            'admin.usuarios.create',
            'admin.usuarios.edit',
            'admin.usuarios.destroy',
            'admin.usuarios.data',

            'admin.roles.permisos',
            'admin.roles.create',
            'admin.roles.edit',
            'admin.roles.destroy',
            'admin.roles.data',
            'admin.roles.permisos.activar.desactivar',
            'admin.roles.permisos.asignar',
            'admin.roles.permisos.quitar',

            'admin.permisos',
            'admin.permisos.create',
            'admin.permisos.edit',
            'admin.permisos.destroy',
            'admin.permisos.data',

            'admin.hojaruta',
            'admin.hojaruta.index',
            'admin.hojaruta.create',
            'admin.hojaruta.edit',
            'admin.hojaruta.data',
            'admin.hojaruta.show',
            'admin.hojaruta.funcionarios',
            'admin.hojaruta.concluir',

            'admin.derivaciones',
            'admin.derivaciones.create',
            'admin.derivaciones.edit',
            'admin.derivaciones.data',
            'admin.derivaciones.show',
            'admin.derivaciones.print',
            'admin.derivaciones.recepcionar',

            'admin.buzon',
            'admin.reportes',
            'admin.reportes.consultar-hoja',
            'admin.reportes.hojas-por-unidad',
            'admin.reportes.total-unidades',
            'admin.anulaciones',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $admin = Role::updateOrCreate(['name' => 'ADMINISTRADOR']);
        $director = Role::updateOrCreate(['name' => 'DIRECTOR']);
        $funcionario = Role::updateOrCreate(['name' => 'FUNCIONARIO']);
        $secretaria = Role::updateOrCreate(['name' => 'SECRETARIA']);

        $admin->syncPermissions(Permission::all());

        $director->syncPermissions([
            'admin.hojaruta',
            'admin.hojaruta.index',
            'admin.hojaruta.show',
            'admin.hojaruta.data',
        ]);

        $funcionario->syncPermissions([
            'admin.hojaruta',
            'admin.hojaruta.show',

            'admin.derivaciones',
            'admin.derivaciones.create',
            'admin.derivaciones.show',
            'admin.derivaciones.recepcionar',

            'admin.buzon',
            'admin.reportes',
            'admin.anulaciones',
        ]);

        $secretaria->syncPermissions(
            'admin.hojaruta',
            'admin.hojaruta.create',
            'admin.hojaruta.show',
            'admin.hojaruta.funcionarios',
            'admin.hojaruta.concluir',

            'admin.derivaciones',
            'admin.derivaciones.create',
            'admin.derivaciones.show',
            'admin.derivaciones.recepcionar',

            'admin.buzon',
            'admin.reportes',
            'admin.anulaciones',
        );
        $adminUser = User::updateOrCreate(
            ['email' => 'admin'],
            [
                'password' => Hash::make('12345678'),
            ]
        );

        $adminRole = Role::where('name', 'ADMINISTRADOR')->first();

        $adminUser->syncRoles([$adminRole]);
        $this->command->info('🔥 Roles y permisos recreados sin errores de FK');
    }

}