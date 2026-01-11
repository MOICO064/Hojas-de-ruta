<?php

namespace App\Http\Controllers;

use App\Models\Anulacion;
use App\Models\HojaRuta;
use App\Models\Derivacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class AnulacionController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'tipo'          => 'required|in:hoja,derivacion',
                'id'            => 'required|integer',
                'justificacion' => 'required|string|min:5|max:1000',
            ], [
                'justificacion.required' => 'La justificación es obligatoria',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();

            $dataBaseAnulacion = [
                'funcionario_id' => $user->funcionario_id,
                'justificacion'  => $request->justificacion,
                'fecha_anulacion' => now(),
            ];
            if ($request->tipo === 'hoja') {

                $hoja = HojaRuta::with('derivaciones')->findOrFail($request->id);

                if ($hoja->estado === 'ANULADO') {
                    return response()->json([
                        'message' => 'La hoja de ruta ya se encuentra anulada'
                    ], 422);
                }

                $hoja->update([
                    'estado' => 'ANULADO',
                ]);

                Anulacion::create(array_merge($dataBaseAnulacion, [
                    'hoja_id' => $hoja->id,
                ]));

                foreach ($hoja->derivaciones as $derivacion) {

                    if ($derivacion->estado === 'ANULADO') {
                        continue;
                    }

                    $derivacion->update([
                        'estado' => 'ANULADO',
                    ]);

                    Anulacion::create(array_merge($dataBaseAnulacion, [
                        'derivacion_id' => $derivacion->id,
                        'hoja_id'       => $hoja->id,
                    ]));
                }
            }

            if ($request->tipo === 'derivacion') {

                $derivacion = Derivacion::with('hojaRuta.derivaciones')
                    ->findOrFail($request->id);

                if ($derivacion->estado === 'ANULADO') {
                    return response()->json([
                        'message' => 'La derivación ya se encuentra anulada'
                    ], 422);
                }

                $derivacion->update([
                    'estado' => 'ANULADO',
                ]);

                Anulacion::create(array_merge($dataBaseAnulacion, [
                    'derivacion_id' => $derivacion->id,
                ]));

                $derivacionAnterior = $derivacion->hojaRuta
                    ->derivaciones
                    ->where('id', '<', $derivacion->id)
                    ->where('estado', '!=', 'ANULADO')
                    ->sortByDesc('id')
                    ->first();

                if ($derivacionAnterior) {
                    $derivacionAnterior->update([
                        'estado' => 'RECEPCIONADO',
                    ]);
                }
            }


            DB::commit();

            return response()->json([
                'message' => 'Anulación registrada correctamente'
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error inesperado',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request)
    {
        $tipo = $request->query('tipo');
        $id   = $request->query('id');

        if (!in_array($tipo, ['hoja', 'derivacion'])) {
            return response()->json(['message' => 'Tipo inválido'], 422);
        }

        $anulacion = Anulacion::query()
            ->when($tipo === 'hoja', fn($q) => $q->where('hoja_id', $id))
            ->when($tipo === 'derivacion', fn($q) => $q->where('derivacion_id', $id))
            ->with('funcionario')
            ->latest('fecha_anulacion')
            ->first();

        if (!$anulacion) {
            return response()->json(['message' => 'No se encontró la anulación'], 404);
        }

        return response()->json([
            'tipo'          => $tipo === 'hoja' ? 'Hoja de Ruta' : 'Derivación',
            'justificacion' => $anulacion->justificacion,
            'usuario'       => $anulacion->funcionario->nombre,
            'fecha'         => $anulacion->fecha_anulacion->format('d/m/Y H:i'),
        ]);
    }
}