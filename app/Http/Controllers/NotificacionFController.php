<?php

namespace App\Http\Controllers;

use App\Models\NotificacionF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionFController extends Controller
{

    public function index()
    {
        return view('admin.notificaciones.index');
    }
    public function todas(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->input('length', 10);
        $start = $request->input('start', 0);     
        $page = ($start / $perPage) + 1;         

        if ($user->hasRole('SECRETARIA')) {
            $unidadId = $user->funcionario->unidad_id ?? null;

            if ($unidadId) {
                $funcionariosIds = \App\Models\Funcionario::where('unidad_id', $unidadId)
                    ->pluck('id')
                    ->toArray();

                $query = NotificacionF::whereIn('funcionario_id', $funcionariosIds)
                    ->orderByDesc('fecha');
            } else {
                $query = NotificacionF::whereRaw('0=1'); // Vacío
            }
        } else {
            $funcionarioId = $user->funcionario_id;

            $query = NotificacionF::where('funcionario_id', $funcionarioId)
                ->orderByDesc('fecha');
        }

        $pag = $query->paginate($perPage, ['*'], 'page', $page);

        $pag->getCollection()->transform(function ($n) {
            return [
                'id' => $n->id,
                'mensaje' => $n->mensaje,
                'leida' => (bool) $n->leido,
                'created_at' => $n->fecha?->toDateTimeString() ?? $n->created_at->toDateTimeString(),
                'hoja_id' => $n->hoja_id,
                'tipo' => $n->tipo,
            ];
        });

        // DataTables espera ciertos campos
        return response()->json([
            'draw' => intval($request->input('draw')),     
            'recordsTotal' => $pag->total(),
            'recordsFiltered' => $pag->total(),
            'data' => $pag->items(),                       
        ]);
    }

    public function marcarTodasLeidas()
    {
        $user = Auth::user();

        if ($user->hasRole('SECRETARIA')) {

            $unidadId = $user->funcionario->unidad_id ?? null;

            if ($unidadId) {
                $funcionariosIds = \App\Models\Funcionario::where('unidad_id', $unidadId)
                    ->pluck('id')
                    ->toArray();

                NotificacionF::whereIn('funcionario_id', $funcionariosIds)
                    ->where('leido', false)
                    ->update(['leido' => true]);
            }
        } else {
            $funcionarioId = $user->funcionario_id;

            NotificacionF::where('funcionario_id', $funcionarioId)
                ->where('leido', false)
                ->update(['leido' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones han sido marcadas como leídas.',
        ]);
    }

    public function noLeidas(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $user = auth()->user();

        if ($user->hasRole('SECRETARIA')) {
            $unidadId = $user->funcionario->unidad_id ?? null;

            if ($unidadId) {
                $funcionariosIds = \App\Models\Funcionario::where('unidad_id', $unidadId)
                    ->pluck('id')
                    ->toArray();

                $notificaciones = NotificacionF::whereIn('funcionario_id', $funcionariosIds)
                    ->where('leido', false)
                    ->orderByDesc('fecha')
                    ->limit(10)
                    ->get();
            } else {
                $notificaciones = collect();
            }
        } else {
            $funcionarioId = $user->funcionario_id;

            $notificaciones = NotificacionF::where('funcionario_id', $funcionarioId)
                ->where('leido', false)
                ->orderByDesc('fecha')
                ->limit(10)
                ->get();
        }

        return response()->json($notificaciones);
    }



    public function marcarLeida($id)
    {
        $notificacion = NotificacionF::where('id', $id)
            ->firstOrFail();

        $notificacion->update([
            'leido' => true
        ]);

        return redirect()->route('admin.hojaruta.show', $notificacion->hoja_id);
    }
}