<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Unidad extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'unidades';

    protected $fillable = [
        'unidad_padre_id',
        'jefe',
        'nombre',
        'codigo',
        'telefono',
        'interno',
        'nivel',
        'estado',
    ];


    /**
     * Unidad padre (para estructura jerárquica)
     */
    public function unidadPadre()
    {
        return $this->belongsTo(Unidad::class, 'unidad_padre_id');
    }

    /**
     * Unidades hijas (dependencias)
     */
    public function unidadesHijas()
    {
        return $this->hasMany(Unidad::class, 'unidad_padre_id');
    }

    /**
     * Funcionarios asociados a esta unidad
     */
    public function funcionarios()
    {
        return $this->hasMany(Funcionario::class, 'unidad_id');
    }

    /**
     * Hojas de ruta emitidas desde esta unidad
     */
    public function hojasRutaOrigen()
    {
        return $this->hasMany(HojaRuta::class, 'unidad_origen_id');
    }

    public static function getTree($id = null)
    {
        // Si se pasa un ID específico
        if ($id) {
            $unit = self::where('id', $id)
                ->where('estado', 'ACTIVO')
                ->first();

            if (!$unit) {
                return []; // No existe o no está activa
            }

            return [
                'id' => $unit->id,
                'nombre' => $unit->nombre,
                'jefe' => $unit->jefe,
                'nivel' => $unit->nivel,
                'children' => self::getTreeChildren($unit->id)
            ];
        }

        // Si no se pasa ID, devolvemos todo desde los nodos raíz
        return self::getTreeChildren();
    }

    /**
     * Función auxiliar para obtener hijos recursivamente
     */
    private static function getTreeChildren($parentId = null)
    {
        $units = self::where('unidad_padre_id', $parentId)
            ->where('estado', 'ACTIVO')
            ->orderBy('nombre')
            ->get();

        $result = [];

        foreach ($units as $unit) {
            $result[] = [
                'id' => $unit->id,
                'nombre' => $unit->nombre,
                'jefe' => $unit->jefe,
                'nivel' => $unit->nivel,
                'children' => self::getTreeChildren($unit->id)
            ];
        }

        return $result;
    }



    /**
     * Eliminación segura: si tiene dependencias, solo cambia a ANULADO
     */
    public function safeDelete()
    {
        if ($this->unidadesHijas()->count() || $this->funcionarios()->count() || $this->hojasRutaOrigen()->count()) {
            $this->estado = 'ANULADO';
            $this->save();
            return false;
        }

        $this->delete();
        return true;
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('unidad')
            ->logOnly(['nombre', 'jefe', 'codigo', 'nivel', 'estado'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {

                $authUser = auth()->user();
                $authUserId = $authUser?->id;
                $authUserName = $authUser?->email;

                $relatedUser = $this->user ?? null;
                $relatedUserId = $relatedUser?->id;
                $relatedUserName = $relatedUser?->usuario;

                return "Unidad: {$this->nombre} | "
                    . "Evento: {$eventName} | "
                    . "Usuario relacionado: " . ($relatedUserName ? "{$relatedUserName} ({$relatedUserId})" : "null") . " | "
                    . "Realizado por: " . ($authUser ? "{$authUserName} ({$authUserId})" : "null");
            });
    }

}
