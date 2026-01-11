<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Derivacion extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'derivaciones';

    /**
     * Atributos asignables masivamente
     */
    protected $fillable = [
        'hoja_id',
        'unidad_origen_id',
        'unidad_destino_id',
        'descripcion',
        'estado',
        'funcionario_id',
        'derivado_por',
        'pdf',
        'fileid',
        'fojas',
        'fecha_derivacion',
        'fecha_recepcion',
    ];

    /**
     * Casteo de atributos
     */
    protected $casts = [
        'fecha_derivacion' => 'datetime',
        'fecha_recepcion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relaciones
     */

    // Hoja de Ruta asociada
    public function hojaRuta()
    {
        return $this->belongsTo(HojaRuta::class, 'hoja_id');
    }

    // Unidad de origen
    public function unidadOrigen()
    {
        return $this->belongsTo(Unidad::class, 'unidad_origen_id');
    }

    // Unidad destino
    public function unidadDestino()
    {
        return $this->belongsTo(Unidad::class, 'unidad_destino_id');
    }

    // Funcionario asociado a la derivación (opcional)
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    // Funcionario que derivó el documento
    public function derivadoPor()
    {
        return $this->belongsTo(Funcionario::class, 'derivado_por');
    }
    /**
     * Configuración del activity log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('funcionario')
            ->logOnly(['unidad_origen_id', 'unidad_destino_id', 'funcionario_id', 'descripcion', 'estado', 'fojas', 'pdf', 'fileid'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $authUser = auth()->user();
                $authUserId = $authUser?->id;
                $authUserEmail = $authUser?->email;

                $relatedFuncionarioName = $this->funcionario?->nombre ?? $this->descripcion;

                return "Derivación: {$relatedFuncionarioName} | Evento: {$eventName} | Realizado por: " .
                    ($authUser ? "{$authUserEmail} ({$authUserId})" : "null");
            });
    }
}
