<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;
use Exception;
class UsuarioController extends Controller
{
    public function index()
    {
        return view('admin.usuarios.index');
    }
    public function data(Request $request)
    {
        $query = User::with('funcionario.unidad', 'roles');

        return DataTables::of($query)
            ->addColumn('funcionario', fn(User $user) => $user->funcionario?->nombre ?? '-')
            ->addColumn('unidad', fn(User $user) => $user->funcionario?->unidad?->nombre ?? '-')
            ->addColumn('rol', fn(User $user) => $user->getRoleNames()->first() ?? '-')
            ->addColumn('acciones', function (User $user) {

                $edit = '<a href="' . route('admin.usuarios.edit', $user->id) . '" 
                        class="btn btn-sm btn-outline-primary d-flex align-items-center me-1" 
                        title="Editar Usuario">
                        <i data-feather="edit-2" class="nav-icon me-1 d-none d-md-inline"></i>
                        <span class="d-none d-md-inline">Editar</span>
                        <i data-feather="edit-2" class="nav-icon d-inline d-md-none"></i>
                    </a>';

                $delete = '';
                if ($user->estado != "INACTIVO") {
                    $delete = '<button type="button" onclick="eliminarUsuario(' . $user->id . ')" 
                                class="btn btn-sm btn-outline-danger d-flex align-items-center" 
                                title="Eliminar Usuario">
                                <i data-feather="trash-2" class="nav-icon me-1 d-none d-md-inline"></i>
                                <span class="d-none d-md-inline">Eliminar</span>
                                <i data-feather="trash-2" class="nav-icon d-inline d-md-none"></i>
                        </button>';
                }

                return '<div class="d-flex flex-wrap gap-2">' . $edit . $delete . '</div>';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }
    public function create()
    {
        // Traemos todos los funcionarios disponibles
        $funcionarios = Funcionario::orderBy('nombre')->get();

        // Traemos todos los roles de Spatie
        $roles = Role::orderBy('name')->get();

        return view('admin.usuarios.form', compact('funcionarios', 'roles'));
    }
    public function store(Request $request)
    {
        try {
            // Validación
            $validator = Validator::make($request->all(), [
                'funcionario_id' => 'required|exists:funcionarios,id',
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|min:6',
                'role' => 'required|exists:roles,name',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }


            $user = User::create([
                'funcionario_id' => $request->funcionario_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'estado' => 'ACTIVO',
            ]);

            // Asignar rol
            $user->assignRole($request->role);

            return response()->json([
                'message' => 'Usuario creado correctamente',
                'user' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Error inesperado: " . $e->getMessage()
            ], 500);
        }
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $funcionarios = Funcionario::orderBy('nombre')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.usuarios.form', compact('user', 'funcionarios', 'roles'));
    }
    public function update(Request $request, User $user)
    {
        try {
            $authUser = auth()->user();

            // Validar que el usuario logeado no pueda ponerse INACTIVO a sí mismo
            if ($authUser->id === $user->id && $request->estado === 'INACTIVO') {
                return response()->json([
                    'errors' => [
                        'estado' => ['No puedes desactivar tu propio usuario mientras estás logeado.']
                    ]
                ], 422);
            }

            // Validación normal
            $validator = Validator::make($request->all(), [
                'funcionario_id' => 'nullable|exists:funcionarios,id',
                'email' => 'required|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:6',
                'role' => 'required|exists:roles,name',
                'estado' => 'required|in:ACTIVO,INACTIVO',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualizar los datos del usuario
            $user->update([
                'funcionario_id' => $request->funcionario_id,
                'email' => $request->email,
                'estado' => $request->estado,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ]);

            // Actualizar rol
            $user->syncRoles([$request->role]);

            return response()->json([
                'message' => 'Usuario actualizado correctamente',
                'user' => $user
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => "Error inesperado: " . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $authUser = auth()->user();

            if ($authUser->id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propio usuario mientras estás logeado.'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente.'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error inesperado: " . $e->getMessage()
            ], 500);
        }
    }

}
