<?php

namespace App\Http\Controllers;

use App\Models\HojaRuta;
use App\Models\Derivacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Unidad;
use Illuminate\Support\Facades\Validator;
class ReportesController extends Controller
{

    public function hojaRuta($id)
    {
        $hojaRuta = HojaRuta::with([
            'unidadOrigen',
            'solicitante',
            'creador',
            'derivaciones' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'derivaciones.unidadOrigen',
            'derivaciones.unidadDestino',
            'derivaciones.funcionario',
            'derivaciones.derivadoPor',
        ])->findOrFail($id);

        $hojaRuta->fecha_impresion = Carbon::now();
        $hojaRuta->save();

        $totalFojas = $hojaRuta->derivaciones->sum('fojas');

        $pdf = Pdf::loadView(
            'admin.reportes.hoja_ruta',
            compact('hojaRuta', 'totalFojas')
        )->setPaper('letter', 'portrait');

        return $pdf->stream(
            'Hoja_Ruta_' . $hojaRuta->idgral . '.pdf'
        );
    }


    public function derivaciones(Request $request)
    {
        $derivacionesIds = $request->query('derivaciones', []);

        if (is_string($derivacionesIds)) {
            $derivacionesIds = explode(',', $derivacionesIds);
        }

        if (empty($derivacionesIds)) {
            return response()->json([
                'message' => 'Debe enviar al menos una derivación'
            ], 422);
        }

        $derivaciones = Derivacion::with([
            'hojaRuta',
            'unidadOrigen',
            'unidadDestino',
            'funcionario',
            'derivadoPor'
        ])
            ->whereIn('id', $derivacionesIds)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($derivaciones->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron derivaciones con esos IDs'
            ], 404);
        }

        $hojasRutaIds = $derivaciones
            ->pluck('hoja_id')
            ->unique()
            ->values();

        HojaRuta::whereIn('id', $hojasRutaIds)
            ->update([
                'fecha_impresion' => Carbon::now()
            ]);
        $pdf = Pdf::loadView(
            'admin.reportes.derivacion',
            compact('derivaciones')
        )->setPaper('letter', 'portrait');

        return $pdf->stream('Hojas_Ruta.pdf');
    }

    public function consultarHoja()
    {
        $unidades = Unidad::whereHas('hojasRutaOrigen')
            ->orderBy('nombre')
            ->get();

        return view('admin.reportes.consultar-hoja', compact('unidades'));
    }

    public function consultarHojaCiudadano()
    {
        $unidades = Unidad::whereHas('hojasRutaOrigen')
            ->orderBy('nombre')
            ->get();

        return view('ciudadano.index', compact('unidades'));
    }
    public function buscarHoja(Request $request)
    {
        // Validación backend
        $request->validate([
            'numero' => 'required|string',
            'unidad_id' => 'required|exists:unidades,id',
            'fecha' => 'required|date',
        ], [
            'numero.required' => 'El número de hoja es obligatorio.',
            'unidad_id.required' => 'Debe seleccionar una unidad.',
            'unidad_id.exists' => 'La unidad seleccionada no es válida.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no es válida.',
        ]);

        $hoja = HojaRuta::with([
            'unidadOrigen',
            'derivaciones.unidadOrigen',
            'derivaciones.unidadDestino'
        ])
            ->where('idgral', $request->numero)
            ->where('unidad_origen_id', $request->unidad_id)
            ->whereDate('fecha_creacion', $request->fecha)
            ->first();

        if ($hoja) {
            $html = view('admin.reportes.partials.resultado-hoja', compact('hoja'))->render();
            return response()->json(['found' => true, 'html' => $html]);
        }

        $html = view('admin.reportes.partials.hoja-no-encontrada')->render();
        return response()->json(['found' => false, 'html' => $html]);
    }

    public function hojasPorUnidad()
    {
        $unidades = Unidad::whereHas('hojasRutaOrigen')
            ->orderBy('nombre')
            ->get();

        return view('admin.reportes.hojas-por-unidad', compact('unidades'));
    }
    public function hojasPorUnidadAjax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unidad_id' => 'required|exists:unidades,id',
            'gestion' => 'required|digits:4|integer|min:2000|max:' . date('Y'),
            'fecha' => 'nullable|date',
        ], [
            'unidad_id.required' => 'La unidad es obligatoria',
            'unidad_id.exists' => 'La unidad seleccionada no es válida',

            'gestion.required' => 'La gestión es obligatoria',
            'gestion.digits' => 'La gestión debe ser un año válido',
            'gestion.integer' => 'La gestión debe ser numérica',

            'fecha.date' => 'La fecha no es válida',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $query = HojaRuta::with([
            'unidadOrigen',
            'derivaciones.unidadDestino',
            'derivaciones.funcionario'
        ])
            ->where('unidad_origen_id', $request->unidad_id)
            ->whereYear('fecha_creacion', $request->gestion);

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_creacion', $request->fecha);
        }

        $hojas = $query
            ->orderBy('fecha_creacion', 'desc')
            ->get();

        return response()->json([
            'html' => view(
                'admin.reportes.partials.resultado-hojas-unidad',
                compact('hojas')
            )->render()
        ]);
    }
    public function totalUnidades()
    {
        return view('admin.reportes.total-hojas-unidad');
    }
    public function hojasPorGestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gestion' => 'required|digits:4|integer|min:2000|max:' . date('Y'),
            'mes' => 'nullable|integer|min:1|max:12',
        ], [
            'gestion.required' => 'La gestión es obligatoria',
            'gestion.digits' => 'La gestión debe ser un año válido',
            'gestion.integer' => 'La gestión debe ser numérica',
            'mes.integer' => 'El mes debe ser un número válido',
            'mes.min' => 'El mes no puede ser menor a 1',
            'mes.max' => 'El mes no puede ser mayor a 12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $query = HojaRuta::with([
            'unidadOrigen',
            'derivaciones.unidadDestino',
            'derivaciones.funcionario'
        ])
            ->where('gestion', $request->gestion);

        if ($request->filled('mes')) {
            $query->whereMonth('fecha_creacion', $request->mes);
        }

        $hojas = $query
            ->orderBy('fecha_creacion', 'desc')
            ->get();

        return response()->json([
            'html' => view(
                'admin.reportes.partials.resultado-hojas-gestion',
                compact('hojas')
            )->render()
        ]);
    }


}