<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unidad;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UnidadController extends Controller
{
    public function index()
    {
        return view('admin.unidades.index');
    }
    public function data(Request $request)
    {
        $query = Unidad::with('unidadPadre', 'unidadesHijas');

        return DataTables::of($query)
            ->addColumn('unidad_padre', fn(Unidad $unidad) => $unidad->unidadPadre?->nombre ?? '-')
            ->addColumn('sub_unidades_count', fn(Unidad $unidad) => $unidad->unidadesHijas->count())
            ->addColumn('acciones', function (Unidad $unidad) {

                $edit = '<a href="' . route('admin.unidades.edit', $unidad->id) . '" 
                class="btn btn-sm btn-outline-primary d-flex align-items-center me-1" 
                title="Editar Unidad">
                <i data-feather="edit" class="nav-icon me-1 d-none d-md-inline"></i>
                <span class="d-none d-md-inline">Editar</span>
                <i data-feather="edit" class="nav-icon d-inline d-md-none"></i>
             </a>';

                $delete = '<button type="button" onclick="eliminarUnidad(' . $unidad->id . ')" 
                    class="btn btn-sm btn-outline-danger d-flex align-items-center" 
                    title="Eliminar Unidad">
                    <i data-feather="trash-2" class="nav-icon me-1 d-none d-md-inline"></i>
                    <span class="d-none d-md-inline">Eliminar</span>
                    <i data-feather="trash-2" class="nav-icon d-inline d-md-none"></i>
               </button>';

                return '<div class="d-flex flex-wrap gap-1">' . $edit . $delete . '</div>';
            })
            ->rawColumns(['acciones'])



            ->make(true);
    }
    public function create()
    {
        $unidades = Unidad::all();
        return view('admin.unidades.form', compact('unidades'));
    }

    public function store(Request $request)
    {
        try {
            // Validación según el schema de la tabla unidades
            $validator = Validator::make($request->all(), [
                'unidad_padre_id' => 'nullable|exists:unidades,id',
                'jefe' => 'nullable|string|max:255',
                'nombre' => 'required|string|max:255',
                'codigo' => 'nullable|string|max:20',
                'telefono' => 'required|integer',
                'celular' => 'required|integer',
                'nivel' => 'required|integer|min:1',
                'estado' => 'required|in:ACTIVO,INACTIVO',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $unidad = Unidad::create([
                'unidad_padre_id' => $request->unidad_padre_id,
                'jefe' => $request->jefe,
                'nombre' => $request->nombre,
                'codigo' => $request->codigo,
                'telefono' => $request->telefono,
                'celular' => $request->celular,
                'nivel' => $request->nivel,
                'estado' => $request->estado,
            ]);

            return response()->json([
                'message' => 'Unidad creada correctamente',
                'unidad' => $unidad
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Error inesperado: " . $e->getMessage()
            ], 500);
        }
    }
    public function edit($id)
    {
        try {
            // Buscar la unidad, si no existe lanza ModelNotFoundException
            $unidad = Unidad::findOrFail($id);

            // Todas las unidades activas menos la que estamos editando
            $unidades = Unidad::where('estado', 'ACTIVO')
                ->where('id', '!=', $unidad->id)
                ->get();

            return view('admin.unidades.form', [
                'unidad' => $unidad,
                'unidades' => $unidades
            ]);
        } catch (ModelNotFoundException $e) {
            // Redirigir al index si no se encuentra la unidad
            return redirect()->route('admin.unidades.index')
                ->with('error', 'La unidad que intentas editar no existe.');
        }
    }

    public function update(Request $request, Unidad $unidad)
    {
        try {
            // Misma validación que en store
            $validator = Validator::make($request->all(), [
                'unidad_padre_id' => 'nullable|exists:unidades,id',
                'jefe' => 'nullable|string|max:255',
                'nombre' => 'required|string|max:255',
                'codigo' => 'nullable|string|max:20',
                'telefono' => 'required|integer',
                'celular' => 'required|integer',
                'nivel' => 'required|integer|min:1',
                'estado' => 'required|in:ACTIVO,INACTIVO',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $unidad->update([
                'unidad_padre_id' => $request->unidad_padre_id,
                'jefe' => $request->jefe,
                'nombre' => $request->nombre,
                'codigo' => $request->codigo,
                'telefono' => $request->telefono,
                'celular' => $request->celular,
                'nivel' => $request->nivel,
                'estado' => $request->estado,
            ]);

            return response()->json([
                'message' => 'Unidad actualizada correctamente',
                'unidad' => $unidad
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Error inesperado: " . $e->getMessage()
            ], 500);
        }
    }
    public function destroy(Unidad $unidad)
    {
        try {
            $unidad->delete();

            return response()->json([
                'success' => true,
                'message' => 'Unidad eliminada correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la unidad: ' . $e->getMessage()
            ], 500);
        }
    }

}
