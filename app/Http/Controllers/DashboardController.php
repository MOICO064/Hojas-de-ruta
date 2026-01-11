<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HojaRuta;
use App\Models\Derivacion;
use App\Models\Unidad;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // =====================
        // Base query según rol
        // =====================
        if ($user->hasRole('FUNCIONARIO')) {
            $hojasQuery       = HojaRuta::where('solicitante_id', $user->id);
            $derivacionesQuery = Derivacion::where('funcionario_id', $user->funcionario_id);
        } elseif ($user->hasRole('SECRETARIA')) {
            $unidadId = $user->funcionario->unidad_id ?? null;

            $hojasQuery = HojaRuta::when($unidadId, fn($q) => $q->where('unidad_origen_id', $unidadId));
            $derivacionesQuery = Derivacion::when($unidadId, fn($q) => $q->whereHas('funcionario', fn($q2) => $q2->where('unidad_id', $unidadId)));
        } else {
            // ADMIN o DIRECTOR → todo
            $hojasQuery = HojaRuta::query();
            $derivacionesQuery = Derivacion::query();
        }

        // =====================
        // Totales de derivaciones
        // =====================
        $totalesDerivaciones = [
            'total'       => $derivacionesQuery->count(),
            'pendientes'  => (clone $derivacionesQuery)->where('estado','PENDIENTE')->count(),
            'en_proceso'  => (clone $derivacionesQuery)->where('estado','RECEPCIONADO')->count(),
            'completadas' => (clone $derivacionesQuery)->where('estado','CONCLUIDO')->count(),
            'anuladas'    => (clone $derivacionesQuery)->where('estado','ANULADO')->count(),
        ];

        // =====================
        // Totales de hojas
        // =====================
        $totales = [
            'total'       => $hojasQuery->count(),
            'pendientes'  => (clone $hojasQuery)->where('estado', 'PENDIENTE')->count(),
            'en_proceso'  => (clone $hojasQuery)->where('estado', 'EN PROCESO')->count(),
            'completadas' => (clone $hojasQuery)->where('estado', 'Concluidos')->count(),
            'urgentes'    => (clone $hojasQuery)->where('urgente', true)->count(),
        ];

        // =====================
        // Hojas por estado
        // =====================
        $porEstado = (clone $hojasQuery)
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->pluck('total','estado');

        // =====================
        // Hojas por unidad (con nombres)
        // =====================
        $porUnidad = (clone $hojasQuery)
            ->select('unidad_origen_id', DB::raw('COUNT(*) as total'))
            ->groupBy('unidad_origen_id')
            ->pluck('total','unidad_origen_id');

        $unidadNombres = Unidad::whereIn('id', $porUnidad->keys())->pluck('nombre','id');
        $porUnidadNombres = $porUnidad->keys()->map(fn($id) => $unidadNombres[$id] ?? 'N/A');

        // =====================
        // Hojas por gestión / año
        // =====================
        $porGestion = (clone $hojasQuery)
            ->select(DB::raw('YEAR(fecha_creacion) as anio'), DB::raw('COUNT(*) as total'))
            ->groupBy('anio')
            ->orderBy('anio')
            ->pluck('total','anio');

        // =====================
        // Top solicitantes con nombre de funcionario
        // =====================
        $topSolicitantes = (clone $hojasQuery)
            ->select('solicitante_id', DB::raw('COUNT(*) as total'))
            ->groupBy('solicitante_id')
            ->orderByDesc('total')
            ->limit(10)
            ->pluck('total','solicitante_id');

        $solicitanteNombres = User::with('funcionario')
            ->whereIn('id', $topSolicitantes->keys())
            ->get()
            ->mapWithKeys(fn($user) => [$user->id => $user->funcionario->nombre ?? 'N/A']);

        $topSolicitantes = $topSolicitantes->keys()->map(fn($id) => $solicitanteNombres[$id] ?? 'N/A')
            ->combine($topSolicitantes->values());

        // =====================
        // Últimas hojas de ruta
        // =====================
        $ultimas = (clone $hojasQuery)->with(['solicitante','unidadOrigen'])
            ->orderByDesc('fecha_creacion')
            ->limit(10)
            ->get();

        // =====================
        // Retornar vista
        // =====================
        return view('admin.index', compact(
            'totales',
            'totalesDerivaciones',
            'porEstado',
            'porUnidad',
            'porUnidadNombres',
            'porGestion',
            'topSolicitantes',
            'ultimas'
        ));
    }
}
