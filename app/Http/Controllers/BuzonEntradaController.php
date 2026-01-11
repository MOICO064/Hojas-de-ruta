<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Derivacion;
use Yajra\DataTables\Facades\DataTables;

class BuzonEntradaController extends Controller
{
    public function index()
    {
        $buzon = "Entrada";
        return view('admin.buzon.index', compact('buzon'));
    }
     public function data(Request $request)
{
    $user = auth()->user();

    $query = Derivacion::with(['hojaRuta', 'unidadOrigen', 'unidadDestino']);

    if ($user->hasRole('ADMINISTRADOR')) {
    } elseif ($user->hasRole('SECRETARIA')) {
    $unidadId = $user->funcionario->unidad_id ;
    if ($unidadId) {
        $query->where('unidad_destino_id', $unidadId);
    }
}
 elseif ($user->hasRole('FUNCIONARIO')) {
        $query->where('funcionario_id', $user->funcionario_id);
    }

    $query->orderByRaw("FIELD(estado, 'PENDIENTE', 'RECEPCIONADO', 'CONCLUIDO', 'ANULADO') ASC")
          ->orderByDesc('fecha_derivacion');

    return DataTables::of($query)
        ->addColumn('numero_general', fn($row) => $row->hojaRuta->idgral ?? '')
        ->addColumn('numero_unidad', fn($row) => $row->hojaRuta->numero_unidad ?? '')
        ->addColumn('asunto', fn($row) => $row->descripcion ?? '')
        ->addColumn('unidad_origen', fn($row) => $row->unidadOrigen->nombre ?? '')
        ->addColumn('unidad_destino', fn($row) => $row->unidadDestino->nombre ?? '')
        ->addColumn('estado', function ($row) {
            $color = match($row->estado) {
                'PENDIENTE' => 'text-warning',
                'RECEPCIONADO' => 'text-primary',
                'CONCLUIDO' => 'text-success',
                'ANULADO' => 'text-danger',
                default => 'text-secondary',
            };
            return '<span class="' . $color . ' font-weight-bold">' . $row->estado . '</span>';
        })
        ->addColumn('urgente', fn($row) => $row->hojaRuta->urgente
            ? '<span class="badge bg-danger">URGENTE</span>'
            : '<span class="badge bg-secondary">NORMAL</span>')
        ->addColumn('gestion', fn($row) => $row->hojaRuta->gestion ?? '')
        ->addColumn('acciones', function ($row) {
            $buzon = 'Entrada';
            return view('admin.buzon.partials.acciones', compact('row', 'buzon'))->render();
        })
        ->rawColumns(['estado', 'urgente', 'acciones'])
        ->make(true);
}

}