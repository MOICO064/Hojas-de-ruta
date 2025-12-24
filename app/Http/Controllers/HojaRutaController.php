<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HojaRuta;
use App\Models\Unidad;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Exception;
class HojaRutaController extends Controller
{
    public function index($gestion = null)
    {
        try {
            return view('admin.hojaruta.index', compact('gestion'));
        } catch (Exception $e) {
            \Log::error('Error al cargar la vista de Hojas de Ruta: ' . $e->getMessage());

            return redirect()->back()->with('error', 'No se pudo cargar la vista de Hojas de Ruta.');
        }
    }


    public function data(Request $request, $gestion = null)
    {
        $user = auth()->user();

        $query = HojaRuta::with(['unidadOrigen', 'solicitante', 'creador', 'derivaciones']);

        if ($user->hasRole('SECRETARIA')) {
            $query->where('unidad_origen_id', $user->unidad_id);
        } elseif ($user->hasRole('FUNCIONARIO')) {
            $query->where(function ($q) use ($user) {
                $q->where('solicitante_id', $user->id)
                    ->orWhereHas('derivaciones', function ($q2) use ($user) {
                        $q2->where('unidad_destino_id', $user->unidad_id);
                    });
            });
        }

        if ($gestion) {
            $query->where('gestion', $gestion);
        }
        $query->orderByRaw("FIELD(estado, 'Pendiente', 'En Proceso', 'Concluido', 'Anulado') ASC")
            ->orderByRaw("urgente DESC");
        return DataTables::of($query)
            ->addColumn('unidad_origen', fn($row) => $row->unidadOrigen->nombre ?? '')
            ->addColumn('solicitante', fn($row) => $row->solicitante->nombre ?? '')
            ->addColumn('creado_por', fn($row) => $row->creador->nombre ?? '')
            ->addColumn('acciones', function ($row) {
                return view('admin.hojaruta.partials.acciones', compact('row'))->render();
            })
            ->editColumn('estado', function ($row) {
                switch ($row->estado) {
                    case 'Pendiente':
                        $color = 'text-warning';
                        break;
                    case 'En Proceso':
                        $color = 'text-primary';
                        break;
                    case 'Concluido':
                        $color = 'text-success';
                        break;
                    case 'Anulado':
                        $color = 'text-danger';
                        break;
                    default:
                        $color = 'text-secondary';
                }
                return '<span class="' . $color . ' font-weight-bold">' . $row->estado . '</span>';
            })
            ->editColumn('urgente', function ($row) {

                if ($row->urgente) {
                    return '<span class="badge bg-danger">URGENTE</span>';
                }
                return '<span class="badge bg-secondary">NORMAL</span>';
            })


            ->rawColumns(['acciones', 'estado', 'urgente'])
            ->make(true);
    }
    public function create()
    {
        try {
            $unidades = Unidad::orderBy('nombre')->get();

            return view('admin.hojaruta.form', compact('unidades'));

        } catch (Exception $e) {
            return redirect()->route('admin.hojaruta.index')
                ->with('error', 'Ocurrió un error al cargar el formulario: ' . $e->getMessage());
        }
    }
    /**
     * Almacenar una nueva Hoja de Ruta
     */
    public function store(Request $request)
    {
        return $this->guardarHoja($request);
    }
    public function edit($id)
    {
        try {
            $hoja = HojaRuta::findOrFail($id);

            $unidades = Unidad::orderBy('nombre')->get();

            return view('admin.hojaruta.form', compact('hoja', 'unidades'));

        } catch (Exception $e) {
            return redirect()->route('admin.hojaruta.index')
                ->with('error', 'Ocurrió un error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar una Hoja de Ruta existente
     */
    public function update(Request $request, $id)
    {
        return $this->guardarHoja($request, $id);
    }


    /**
     * Método común para crear o actualizar
     */
    private function guardarHoja(Request $request, $id = null)
    {
        try {
            $user = auth()->user();
            $funcionario = $user->funcionario ?? null;

            // Validación base
            $validator = Validator::make($request->all(), [
                'externo' => 'required|boolean',
                'nombre_solicitante' => 'nullable|string|max:255',
                'unidad_origen_id' => 'nullable|exists:unidades,id',
                'solicitante_id' => 'nullable|exists:funcionarios,id',
                'cite' => 'nullable|string|max:255',
                'prioridad' => 'required|boolean',
                'asunto' => 'required|string|max:500',
            ]);

            $validator->after(function ($validator) use ($request, $funcionario) {
                $isExterno = $request->boolean('externo', false);

                if ($isExterno && empty($request->nombre_solicitante)) {
                    $validator->errors()->add('nombre_solicitante', 'El nombre del solicitante externo es obligatorio.');
                }

                if (!$isExterno) {

                    if (empty($request->unidad_origen_id)) {
                        $validator->errors()->add('unidad_origen_id', 'La unidad de origen es obligatoria.');
                    }

                    if (empty($request->solicitante_id)) {
                        $validator->errors()->add('solicitante_id', 'Debe seleccionar un solicitante.');
                    }
                }
            });

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $isExterno = $request->boolean('externo', false);
            $unidadOrigenId = $funcionario->unidad_id;

            if ($id) {
                $hoja = HojaRuta::findOrFail($id);
                $idgral = $hoja->idgral;
                $numeroUnidad = $hoja->numero_unidad;
            } else {
                $idgral = HojaRuta::max('idgral') + 1;
                $unidad = Unidad::find($funcionario->unidad_id);
                if ($unidad) {
                    $numeroUnidad = $unidad->numero_unidad_actual + 1;
                    $unidad->numero_unidad_actual = $numeroUnidad;
                    $unidad->save();
                }
            }

            $datos = [
                'idgral' => $idgral,
                'numero_unidad' => $numeroUnidad,
                'externo' => $isExterno,
                'nombre_solicitante' => $isExterno ? $request->nombre_solicitante : null,
                'unidad_origen_id' => $unidadOrigenId,
                'solicitante_id' => $isExterno ? null : $request->solicitante_id,
                'fecha_creacion' => now(),
                'cite' => $request->cite,
                'urgente' => $request->boolean('prioridad'),
                'asunto' => strtoupper($request->asunto),
                'estado' => 'PENDIENTE',
                'gestion' => config('sistema.gestion_actual', date('Y')),
                'creado_por' => $user->id,
                'fecha_impresion' => null,
            ];

            if ($id) {
                $hoja->update($datos);
                $message = 'Hoja de Ruta actualizada correctamente';
            } else {
                $hoja = HojaRuta::create($datos);
                $message = 'Hoja de Ruta creada correctamente';
            }

            return response()->json([
                'message' => $message,
                'hoja' => $hoja
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => "Error inesperado: " . $e->getMessage()
            ], 500);
        }
    }


}