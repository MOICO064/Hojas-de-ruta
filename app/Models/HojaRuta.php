<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class HojaRuta extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'hoja_ruta';

    /**
     * Atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'idgral',
        'numero_unidad',
        'externo',
        'nombre_solicitante',
        'unidad_origen_id',
        'solicitante_id',
        'fecha_creacion',
        'cite',
        'urgente',
        'asunto',
        'estado',
        'gestion',
        'creado_por',
        'fecha_impresion',
    ];

    /**
     * Casteo de atributos a tipos nativos.
     */
    protected $casts = [
        'fecha_creacion' => 'date',
        'fecha_impresion' => 'datetime',
        'externo' => 'boolean',
        'urgente' => 'boolean',
    ];

    /**
     * Unidad de origen de la hoja de ruta
     */
    public function unidadOrigen()
    {
        return $this->belongsTo(Unidad::class, 'unidad_origen_id', 'id');
    }

    /**
     * Solicitante interno (funcionario)
     */
    public function solicitante()
    {
        return $this->belongsTo(Funcionario::class, 'solicitante_id', 'id');
    }

    /**
     * Usuario que creó la hoja de ruta
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por', 'id');
    }

    /**
     * Derivaciones de la hoja de ruta
     */
    public function derivaciones()
    {
        return $this->hasMany(Derivacion::class, 'hoja_id', 'id');
    }

    /**
     * Anulaciones de la hoja de ruta
     */
    public function anulaciones()
    {
        return $this->hasMany(Anulacion::class, 'hoja_id', 'id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('hoja_ruta')
            ->logOnly([
                'idgral',
                'numero_unidad',
                'nombre_solicitante',
                'unidad_origen_id',
                'solicitante_id',
                'cite',
                'urgente',
                'asunto',
                'estado',
                'gestion',
                'fecha_creacion',
                'fecha_impresion',
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $authUser = auth()->user();
                $authUserId = $authUser?->id;
                $authUserEmail = $authUser?->email;

                $solicitanteName = $this->solicitante?->nombre ?? $this->nombre_solicitante;

                return "Hoja de Ruta: {$solicitanteName} | Evento: {$eventName} | Realizado por: " .
                    ($authUser ? "{$authUserEmail} ({$authUserId})" : "null");
            });
    }
}
