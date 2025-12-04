<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class RolesAndPermisosController extends Controller
{
    public function rolesIndex()
    {
        return view('admin.roles.index');
    }
    public function rolesData(Request $request)
    {
        $query = Role::with('permissions');

        // Roles protegidos
        $protegidos = ['SECRETARIA', 'DIRECTOR', 'FUNCIONARIO', 'ADMINISTRADOR'];

        return DataTables::of($query)
            ->addColumn('nombre', fn(Role $role) => $role->name)

            ->addColumn('permisos_count', fn(Role $role) => $role->permissions->count())

            ->addColumn('permisos', function (Role $role) {
                if ($role->permissions->isEmpty()) {
                    return '<span class="badge bg-secondary">Sin permisos</span>';
                }

                return $role->permissions->pluck('name')
                    ->map(fn($p) => "<span class='badge bg-info me-1'>$p</span>")
                    ->implode(' ');
            })

            ->addColumn('acciones', function (Role $role) use ($protegidos) {

                $verPermisos = '
                <a href="' . route('admin.roles.permisos', $role->id) . '" 
                    class="btn btn-sm btn-outline-info d-flex align-items-center me-1"
                    title="Ver permisos del rol">
                    <i data-feather="shield" class="nav-icon me-1 d-none d-md-inline"></i>
                    <span class="d-none d-md-inline">Permisos</span>
                    <i data-feather="shield" class="nav-icon d-inline d-md-none"></i>
                </a>';

                $edit = '';
                $delete = '';

                // Solo mostrar botones si NO es rol protegido
                if (!in_array(strtoupper($role->name), $protegidos)) {
                    $edit = '
                    <a href="' . route('admin.roles.edit', $role->id) . '" 
                        class="btn btn-sm btn-outline-primary d-flex align-items-center me-1"
                        title="Editar Rol">
                        <i data-feather="edit-2" class="nav-icon me-1 d-none d-md-inline"></i>
                        <span class="d-none d-md-inline">Editar</span>
                        <i data-feather="edit-2" class="nav-icon d-inline d-md-none"></i>
                    </a>';

                    $delete = '
                    <button type="button"
                            onclick="eliminarRol(' . $role->id . ')" 
                            class="btn btn-sm btn-outline-danger d-flex align-items-center"
                            title="Eliminar Rol">
                        <i data-feather="trash-2" class="nav-icon me-1 d-none d-md-inline"></i>
                        <span class="d-none d-md-inline">Eliminar</span>
                        <i data-feather="trash-2" class="nav-icon d-inline d-md-none"></i>
                    </button>';
                }

                return '<div class="d-flex flex-wrap gap-2">' . $verPermisos . $edit . $delete . '</div>';
            })

            ->rawColumns(['permisos', 'acciones'])
            ->make(true);
    }
    public function rolesCreate()
    {
        return view('admin.roles.form');
    }

    public function rolesStore(Request $request)
    {
        try {
            // Validación
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:roles,name',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crear rol
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web', // opcional, predeterminado
            ]);

            return response()->json([
                'message' => 'Rol creado correctamente',
                'role_id' => $role->id
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => "Error inesperado: " . $e->getMessage()
            ], 500);
        }
    }
    public function rolesEdit($id)
    {
        $role = Role::findOrFail($id);

        return view('admin.roles.form', [
            'role' => $role
        ]);
    }
    public function rolesUpdate(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            // Validación
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:roles,name,' . $role->id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualizar rol
            $role->update([
                'name' => strtoupper($request->name), // nombre en mayúsculas
            ]);

            return response()->json([
                'message' => 'Rol actualizado correctamente',
                'role_id' => $role->id
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => "Error inesperado: " . $e->getMessage()
            ], 500);
        }
    }
    public function rolesDestroy($id)
    {
        try {
            $role = Role::findOrFail($id);

            // Roles protegidos
            $protectedRoles = ['SECRETARIA', 'DIRECTOR', 'FUNCIONARIO', 'ADMINISTRADOR'];

            // Revisar si el rol es protegido
            if (in_array(strtoupper($role->name), $protectedRoles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este rol está protegido y no puede ser eliminado.'
                ]);
            }

            // Revisar si el rol está asignado a algún usuario
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un rol que tiene usuarios asignados.'
                ]);
            }

            // Eliminar rol
            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado correctamente.'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage()
            ], 500);
        }
    }
    public function permisos($roleId)
    {
        try {

            $role = Role::findOrFail($roleId);

            $permisos = Permission::all();

            $permisosAsignados = $role->permissions->pluck('id')->toArray();
            $permisosData = $permisos->map(function ($permiso) use ($permisosAsignados) {
                return (object) [
                    'id' => $permiso->id,
                    'name' => $permiso->name,
                    'asignado' => in_array($permiso->id, $permisosAsignados)
                ];
            });


            return view('admin.roles.permisos', [
                'role' => $role,
                'permisos' => $permisosData
            ]);

        } catch (Exception $e) {

            return redirect()->route('admin.roles.index')
                ->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function togglePermiso(Request $request, Role $role, Permission $permission)
    {
        try {

            $estado = $request->input('estado');

            if ($estado) {
                // Asignar permiso si no lo tiene
                if (!$role->hasPermissionTo($permission->name)) {
                    $role->givePermissionTo($permission->name);
                }
                $mensaje = "Permiso '{$permission->name}' activado para el rol '{$role->name}'.";
            } else {
                // Quitar permiso si lo tiene
                if ($role->hasPermissionTo($permission->name)) {
                    $role->revokePermissionTo($permission->name);
                }
                $mensaje = "Permiso '{$permission->name}' desactivado para el rol '{$role->name}'.";
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage()
            ], 500);
        }
    }
    public function asignarTodos(Role $role)
    {
        try {

            $permisos = Permission::pluck('id')->toArray();

            if (empty($permisos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No existen permisos para asignar.'
                ], 404);
            }

            $role->syncPermissions($permisos);

            return response()->json([
                'success' => true,
                'message' => 'Todos los permisos fueron asignados correctamente.'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function quitarTodos(Role $role)
    {
        try {

            if (strtoupper($role->name) === 'ADMINISTRADOR') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este rol está protegido y no se pueden quitar permisos masivamente.'
                ], 403);
            }

            $role->syncPermissions([]);

            return response()->json([
                'success' => true,
                'message' => 'Todos los permisos fueron removidos correctamente.'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function permisosIndex()
    {
        return view('admin.permisos.index');
    }
    public function permisosData(Request $request)
    {
        $query = Permission::query();

        return DataTables::of($query)
            ->addColumn('nombre', fn(Permission $permiso) => $permiso->name)
            ->addColumn('guard', fn(Permission $permiso) => $permiso->guard_name)
            ->addColumn('descripcion', fn(Permission $permiso) => $permiso->description ?? '-')
            ->addColumn('roles_count', fn(Permission $permiso) => $permiso->roles->count())
            ->addColumn('roles', function (Permission $permiso) {
                if ($permiso->roles->isEmpty()) {
                    return '<span class="badge bg-secondary">Sin roles</span>';
                }

                return $permiso->roles->pluck('name')
                    ->map(fn($r) => "<span class='badge bg-info me-1'>$r</span>")
                    ->implode(' ');
            })
            ->addColumn('acciones', function (Permission $permiso) {
                $edit = '
            <a href="' . route('admin.permisos.edit', $permiso->id) . '" 
               class="btn btn-sm btn-outline-primary d-flex align-items-center me-1"
               title="Editar Permiso">
                <i data-feather="edit-2" class="nav-icon me-1 d-none d-md-inline"></i>
                <span class="d-none d-md-inline">Editar</span>
                <i data-feather="edit-2" class="nav-icon d-inline d-md-none"></i>
            </a>';

                $delete = '
            <button type="button"
                    onclick="eliminarPermiso(' . $permiso->id . ')" 
                    class="btn btn-sm btn-outline-danger d-flex align-items-center"
                    title="Eliminar Permiso">
                <i data-feather="trash-2" class="nav-icon me-1 d-none d-md-inline"></i>
                <span class="d-none d-md-inline">Eliminar</span>
                <i data-feather="trash-2" class="nav-icon d-inline d-md-none"></i>
            </button>';

                return '<div class="d-flex flex-wrap gap-2">' . $edit . $delete . '</div>';
            })
            ->rawColumns(['roles', 'acciones'])
            ->make(true);
    }
    public function permisosCreate()
    {
        return view('admin.permisos.form');
    }
    public function permisosStore(Request $request)
    {
        try {
            // Validación
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:permissions,name',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crear permiso
            $permiso = Permission::create([
                'name' => $request->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permiso creado correctamente',
                'permiso' => $permiso,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function permisosEdit($id)
    {
        try {
            // Obtener el permiso por ID
            $permiso = Permission::findOrFail($id);

            // Retornar la vista con los datos del permiso
            return view('admin.permisos.form', [
                'permiso' => $permiso
            ]);

        } catch (Exception $e) {
            // En caso de error, redirigir al listado con mensaje
            return redirect()->route('admin.permisos.index')
                ->with('error', 'No se pudo cargar el permiso: ' . $e->getMessage());
        }
    }
    public function permisosUpdate(Request $request, $id)
    {
        try {
            // Validación
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:permissions,name,' . $id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Buscar el permiso
            $permiso = Permission::findOrFail($id);
            $permiso->name = $request->name; // se guarda tal cual viene
            $permiso->save();

            return response()->json([
                'success' => true,
                'message' => 'Permiso actualizado correctamente.',
                'permiso' => $permiso
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function permisosDestroy($id)
    {
        try {
            $permiso = Permission::findOrFail($id);

            // Verificar si el permiso está asignado a algún rol
            if ($permiso->roles()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar este permiso porque está asignado a uno o más roles.'
                ], 403);
            }

            $permiso->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permiso eliminado correctamente.'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Permiso no encontrado.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage(),
            ], 500);
        }
    }
}
