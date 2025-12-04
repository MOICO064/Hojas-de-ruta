<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HojaRuta;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
class HojaRutaController extends Controller
{
    public function index($gestion = null)
    {
        try {
            return view('admin.hojaruta.index', compact('gestion'));
        } catch (\Exception $e) {
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

        return DataTables::of($query)
            ->addColumn('unidad_origen', fn($row) => $row->unidadOrigen->nombre ?? '')
            ->addColumn('solicitante', fn($row) => $row->solicitante->nombre ?? '')
            ->addColumn('creado_por', fn($row) => $row->creador->nombre ?? '')
            ->addColumn('acciones', function ($row) {
                return view('admin.hojaruta.partials.acciones', compact('row'))->render();
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }
    public function create()
    {
        try {
            // Obtener todas las unidades
            $unidades = \App\Models\Unidad::orderBy('nombre')->get();

            // Obtener todos los funcionarios
            $funcionarios = \App\Models\Funcionario::orderBy('nombre')->get();

            // Retornar la vista con los datos
            return view('admin.hojaruta.form', compact('unidades', 'funcionarios'));

        } catch (\Exception $e) {
            // Redirigir con error si ocurre algo
            return redirect()->route('admin.hojaruta.index')
                ->with('error', 'Ocurrió un error al cargar el formulario: ' . $e->getMessage());
        }
    }

}
