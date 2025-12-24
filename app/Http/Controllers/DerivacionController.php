<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HojaRuta;
use App\Models\Derivacion;
use App\Models\Unidad;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
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
                optional($row->fecha_derivacion)->format('d/m/Y')
            )

            ->editColumn(
                'fecha_recepcion',
                fn($row) =>
                $row->fecha_recepcion
                    ? $row->fecha_recepcion->format('d/m/Y')
                    : '<span class="text-muted">Pendiente</span>'
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
            $unidades = Unidad::orderBy('nombre')->get();

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

    /**
     * Método común para crear derivación
     */
    private function guardarDerivacion(Request $request)
    {
        try {
            $user = auth()->user();

            // Validación
            $validator = Validator::make($request->all(), [
                'hoja_id' => 'required|exists:hoja_ruta,id',
                'unidad_origen_id' => 'nullable|exists:unidades,id',
                'unidad_destino_id' => 'required|exists:unidades,id',
                'descripcion' => 'required|string|max:1000',
                'estado' => 'nullable|string|max:50',
                'funcionario_id' => 'nullable|exists:funcionarios,id',
                'pdf' => 'nullable|string', 
                'fileid' => 'nullable|string',
                'fojas' => 'nullable|string|max:255',
                'fecha_derivacion' => 'nullable|date',
                'fecha_recepcion' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $derivacion = Derivacion::create([
                'hoja_id' => $request->hoja_id,
                'unidad_origen_id' => $request->unidad_origen_id ?? $user->funcionario->unidad_id ?? null,
                'unidad_destino_id' => $request->unidad_destino_id,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado ?? 'PENDIENTE',
                'funcionario_id' => $request->funcionario_id,
                'pdf' => $request->pdf,
                'fileid' => $request->fileid,
                'fojas' => $request->fojas,
                'fecha_derivacion' => $request->fecha_derivacion ?? now(),
                'fecha_recepcion' => $request->fecha_recepcion,
            ]);

            return response()->json([
                'message' => 'Derivación creada correctamente',
                'derivacion' => $derivacion
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error inesperado: ' . $e->getMessage()
            ], 500);
        }
    }

}