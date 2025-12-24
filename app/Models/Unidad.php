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
        'numero_unidad_actual',
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

    /**
     * Obtener estructura jerárquica de unidades
     */
    public static function getTree($id = null)
    {
        if ($id) {
            $unit = self::where('id', $id)
                ->where('estado', 'ACTIVO')
                ->first();

            if (!$unit)
                return [];

            return [
                'id' => $unit->id,
                'nombre' => $unit->nombre,
                'jefe' => $unit->jefe,
                'nivel' => $unit->nivel,
                'numero_unidad_actual' => $unit->numero_unidad_actual,
                'children' => self::getTreeChildren($unit->id),
            ];
        }

        return self::getTreeChildren();
    }

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
                'numero_unidad_actual' => $unit->numero_unidad_actual,
                'children' => self::getTreeChildren($unit->id),
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

    /**
     * Opciones de log de actividad
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('unidad')
            ->logOnly(['nombre', 'jefe', 'codigo', 'nivel', 'numero_unidad_actual', 'estado'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $authUser = auth()->user();
                $authUserId = $authUser?->id;
                $authUserName = $authUser?->email;

                return "Unidad: {$this->nombre} | Evento: {$eventName} | Realizado por: "
                    . ($authUser ? "{$authUserName} ({$authUserId})" : "null");
            });
    }
}
