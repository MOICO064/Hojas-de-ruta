<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HojaRuta;
use App\Models\Derivacion;
use App\Models\Unidad;
use App\Models\Funcionario;
use App\Models\NotificacionF;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class DerivacionController extends Controller
{

    public function index(HojaRuta $hoja)
    {
        try {
            return view('admin.derivacion.index', compact('hoja'));
        } catch (Exception $e) {
            \Log::error(
                'Error al cargar la vista de Derivaciones | Hoja ID: ' . $hoja->id .
                ' | ' . $e->getMessage()
            );

            return redirect()->back()
                ->with('error', 'No se pudo cargar la vista de Derivaciones.');
        }
    }
    public function data(Request $request, HojaRuta $hoja)
    {

        $query = Derivacion::with([
            'unidadOrigen',
            'unidadDestino',
            'funcionario'
        ])
            ->where('hoja_id', $hoja->id);


        $query->orderByRaw("
        FIELD(
            estado,
            'PENDIENTE',
            'DERIVADO',
            'RECEPCIONADO',
            'CONCLUIDO',
            'ANULADO'
        ) ASC
    ")
            ->orderBy('fecha_derivacion', 'DESC');

        return DataTables::of($query)

            ->addColumn(
                'unidad_origen',
                fn($row) =>
                $row->unidadOrigen->nombre ?? ''
            )

            ->addColumn(
                'unidad_destino',
                fn($row) =>
                $row->unidadDestino->nombre ?? ''
            )

            ->addColumn(
                'funcionario',
                fn($row) =>
                $row->funcionario->nombre ?? '<span class="text-muted">No asignado</span>'
            )

            ->editColumn(
                'fecha_derivacion',
                fn($row) =>
                optional($row->fecha_derivacion)->format('d/m/Y H:i')
            )

            ->editColumn(
                'fecha_recepcion',
                fn($row) =>
                $row->fecha_recepcion
                ? $row->fecha_recepcion->format('d/m/Y H:i')
                : '<span class="text-muted">No recepcionado</span>'
            )

            ->editColumn('estado', function ($row) {
                switch ($row->estado) {
                    case 'PENDIENTE':
                        $class = 'badge bg-warning text-dark';
                        break;
                    case 'DERIVADO':
                        $class = 'badge bg-info';
                        break;
                    case 'RECEPCIONADO':
                        $class = 'badge bg-primary';
                        break;
                    case 'CONCLUIDO':
                        $class = 'badge bg-success';
                        break;
                    case 'ANULADO':
                        $class = 'badge bg-danger';
                        break;
                    default:
                        $class = 'badge bg-secondary';
                }

                return '<span class="' . $class . '">' . $row->estado . '</span>';
            })

            ->addColumn('acciones', function ($row) use ($hoja) {
                return view(
                    'admin.derivacion.partials.acciones',
                    compact('row', 'hoja')
                )->render();
            })

            ->rawColumns([
                'estado',
                'fecha_recepcion',
                'acciones',
                'funcionario'
            ])

            ->make(true);
    }
    public function create(HojaRuta $hoja)
    {

        try {
            $unidadFuncionarioId = auth()->user()->funcionario->unidad_id;

            $unidades = Unidad::where('estado', 'ACTIVO')
                ->where('id', '!=', $unidadFuncionarioId)
                ->whereHas('funcionarios')
                ->orderBy('nombre')
                ->get();

            return view('admin.derivacion.form', compact('unidades', 'hoja'));
        } catch (Exception $e) {
            return redirect()->route('admin.buzon.salida')
                ->with('error', 'Ocurrió un error al cargar el formulario: ' . $e->getMessage());
        }
    }
    public function store(Request $request)
    {
        return $this->guardarDerivacion($request);
    }
    public function edit(HojaRuta $hoja, Derivacion $derivacion)
    {

        $unidadFuncionarioId = auth()->user()->funcionario->unidad_id;

        $unidades = Unidad::where('estado', 'ACTIVO')
            ->where('id', '!=', $unidadFuncionarioId)
            ->whereHas('funcionarios')
            ->orderBy('nombre')
            ->get();


        return view('admin.derivacion.form-edit', [
            'hoja' => $hoja,
            'derivacion' => $derivacion,
            'unidades' => $unidades,
        ]);
    }
    public function update(Request $request, $hojaId, Derivacion $derivacion)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'unidad_id' => 'required|exists:unidades,id',
                'funcionario_id' => 'required|exists:funcionarios,id',
                'descripcion' => 'required|string|max:1000',
                'pdf' => 'nullable|string',
                'fojas' => 'nullable|integer|min:1',
            ], [
                'unidad_id.required' => 'La unidad destino es obligatoria',
                'funcionario_id.required' => 'El funcionario es obligatorio',
                'fojas.required_with' => 'Las fojas son obligatorias cuando se adjunta un PDF',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $derivacion->unidad_destino_id = $request->unidad_id;
            $derivacion->funcionario_id = $request->funcionario_id;
            $derivacion->descripcion = strtoupper($request->descripcion);
            $derivacion->fojas = $request->fojas ?? $derivacion->fojas;

            if ($request->pdf) {
                $derivacion->pdf = "https://drive.google.com/file/d/{$request->pdf}/view";
                $derivacion->fileid = $request->pdf;
            }

            $derivacion->save();

            DB::commit();
            return response()->json([
                'message' => 'Derivación actualizada correctamente',
                'data' => $derivacion,
                'derivacion_id' => $derivacion->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    private function guardarDerivacion(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'hoja_id' => 'required|exists:hoja_ruta,id',
                'descripcion' => 'required|string|max:1000',
                'pdf' => 'nullable|string',
                'fojas' => 'required_with:pdf|max:255',
                'destinatarios' => 'required|array|min:1',
                'destinatarios.*.unidad_destino_id' => 'required|exists:unidades,id',
                'destinatarios.*.funcionario_id' => 'required|exists:funcionarios,id|distinct', // <-- aquí
            ], [
                'destinatarios.required' => 'Debe agregar al menos un destinatario',
                'destinatarios.*.unidad_destino_id.required' => 'La unidad destino es obligatoria',
                'destinatarios.*.funcionario_id.required' => 'El funcionario es obligatorio',
                'destinatarios.*.funcionario_id.distinct' => 'No puede seleccionar el mismo funcionario más de una vez',
                'fojas.required_with' => 'Las fojas son obligatorias cuando se adjunta un PDF',
            ]);


            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $derivacionAnterior = Derivacion::where('hoja_id', $request->hoja_id)
                ->where('estado', '=', 'RECEPCIONADO')
                ->latest('id')
                ->first();

            $derivaciones = [];
            $pdfLink = $request->pdf
                ? "https://drive.google.com/file/d/{$request->pdf}/view"
                : null;

            foreach ($request->destinatarios as $destino) {

                $derivacion = Derivacion::create([
                    'hoja_id' => $request->hoja_id,
                    'unidad_origen_id' => $request->unidad_origen_id
                        ?? $user->funcionario->unidad_id
                        ?? null,
                    'unidad_destino_id' => $destino['unidad_destino_id'],
                    'funcionario_id' => $destino['funcionario_id'],
                    'descripcion' => $request->descripcion,
                    'estado' => 'PENDIENTE',
                    'pdf' => $pdfLink,
                    'fileid' => $request->pdf,
                    'fojas' => $request->fojas,
                    'fecha_derivacion' => now(),
                    'fecha_recepcion' => null,
                    'derivado_por' => $user->funcionario_id,
                ]);

                $derivaciones[] = Derivacion::with([
                    'funcionario',
                    'unidadDestino',
                    'unidadOrigen'
                ])->find($derivacion->id);

                $notificacion = NotificacionF::create([
                    'hoja_id' => $request->hoja_id,
                    'funcionario_id' => $destino['funcionario_id'],
                    'tipo' => 'DERIVACION',
                    'mensaje' => "Tiene una nueva derivación pendiente de la hoja #{$request->hoja_id}",
                    'fecha' => now(),
                    'leido' => false,
                ]);


                event(new \App\Events\NuevaNotificacion($notificacion));
            }

            if ($derivacionAnterior) {
                $derivacionAnterior->update([
                    'estado' => 'CONCLUIDO'
                ]);
            }

            HojaRuta::where('id', $request->hoja_id)
                ->update([
                    'estado' => 'En Proceso',
                ]);

            DB::commit();

            return response()->json([
                'message' => 'Derivaciones creadas correctamente',
                'total' => count($derivaciones),
                'derivaciones' => $derivaciones
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function recepcionar($id)
    {
        try {
            $derivacion = Derivacion::findOrFail($id);

            if ($derivacion->estado === 'ANULADO') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede recepcionar una derivación anulada'
                ], 422);
            }

            $derivacion->update([
                'estado' => 'RECEPCIONADO',
                'fecha_recepcion' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Derivación recepcionada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage()
            ], 500);
        }
    }
    public function funcionarios($unidadId)
    {
        $funcionarios = Funcionario::where('unidad_id', $unidadId)
            ->select('id', 'nombre')
            ->get();

        return response()->json($funcionarios);
    }
}