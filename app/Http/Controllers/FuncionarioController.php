<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Exception;
class FuncionarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.funcionarios.index');
    }

    public function data(Request $request)
    {
        $query = Funcionario::with('unidad'); // relación: funcionario pertenece a unidad

        return DataTables::of($query)
            ->addColumn('unidad', fn($f) => $f->unidad?->nombre ?? '-')
            ->addColumn('acciones', function (Funcionario $f) {

                $edit = '<a href="' . route('admin.funcionarios.edit', $f->id) . '" 
                        class="btn btn-sm btn-outline-primary d-flex align-items-center me-1" 
                        title="Editar Funcionario">
                        <i data-feather="edit-2" class="nav-icon me-1 d-none d-md-inline"></i>
                        <span class="d-none d-md-inline">Editar</span>
                        <i data-feather="edit-2" class="nav-icon d-inline d-md-none"></i>
                    </a>';

                $delete = '';
                if ($f->estado !== 'ANULADO') {
                    $delete = '<button type="button" onclick="eliminarFuncionario(' . $f->id . ')" 
                                class="btn btn-sm btn-outline-danger d-flex align-items-center" 
                                title="Eliminar Funcionario">
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unidades = Unidad::where('estado', 'ACTIVO')->get();

        return view('admin.funcionarios.form', [
            'unidades' => $unidades
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validación según el esquema de la tabla funcionarios
            $validator = Validator::make($request->all(), [
                'unidad_id' => 'required|exists:unidades,id',
                'ci' => 'required|string|max:20|unique:funcionarios,ci',
                'nombre' => 'required|string|max:255',
                'cargo' => 'nullable|string|max:255',
                'nro_item' => 'nullable|string|max:255',
                'celular' => 'nullable|string|max:30',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $funcionarioData = [
                'unidad_id' => $request->unidad_id,
                'ci' => $request->ci,
                'nombre' => $request->nombre,
                'cargo' => $request->cargo,
                'nro_item' => $request->nro_item,
                'celular' => $request->celular,
            ];


            $funcionario = Funcionario::create($funcionarioData);

            return response()->json([
                'message' => 'Funcionario registrado correctamente',
                'funcionario' => $funcionario
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => "Error inesperado: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Funcionario $funcionario)
    {
        // Obtener todas las unidades activas para el select
        $unidades = Unidad::orderBy('nombre')->get();

        // Retornar la vista del formulario pasando el funcionario y las unidades
        return view('admin.funcionarios.form', [
            'funcionario' => $funcionario,
            'unidades' => $unidades,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Funcionario $funcionario)
    {
        try {
            // Validación según el schema de la tabla funcionarios
            $validator = Validator::make($request->all(), [
                'unidad_id' => 'required|exists:unidades,id',
                'ci' => 'required|string|max:20|unique:funcionarios,ci,' . $funcionario->id,
                'nombre' => 'required|string|max:255',
                'cargo' => 'nullable|string|max:255',
                'nro_item' => 'nullable|string|max:255',
                'celular' => 'nullable|string|max:30',
                'estado' => 'required|in:ACTIVO,ANULADO'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualizar los datos del funcionario
            $funcionario->update([
                'unidad_id' => $request->unidad_id,
                'ci' => $request->ci,
                'nombre' => $request->nombre,
                'cargo' => $request->cargo,
                'nro_item' => $request->nro_item,
                'celular' => $request->celular,
                'estado' => $request->estado,
            ]);

            return response()->json([
                'message' => 'Funcionario actualizado correctamente',
                'funcionario' => $funcionario
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => "Error inesperado: " . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Funcionario $funcionario)
    {
        try {
            // Comprobar si el funcionario tiene relaciones que impidan eliminar
            $tieneRelaciones = $funcionario->user()->exists() || $funcionario->hojasRutaSolicitante()->exists();

            if ($tieneRelaciones) {
                // Cambiar estado a ANULADO
                $funcionario->update(['estado' => 'ANULADO']);

                return response()->json([
                    'message' => 'El funcionario tiene relaciones. Se cambió su estado a ANULADO.'
                ]);
            } else {
                // Eliminar físicamente
                $funcionario->delete();

                return response()->json([
                    'message' => 'Funcionario eliminado correctamente.'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => "Error inesperado: " . $e->getMessage()
            ], 500);
        }
    }

}
