<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Funcionario extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'funcionarios';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'unidad_id',
        'ci',
        'nombre',
        'cargo',
        'nro_item',
        'estado',
        'celular',
    ];

    /**
     * Los atributos que deben ser casteados a tipos nativos.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con la secretaria (un funcionario pertenece a una secretaria)
     */

    /**
     * Relación con la unidad (un funcionario pertenece a una unidad)
     */
    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'unidad_id', 'id');
    }

    /**
     * Relación con el usuario (opcional, un funcionario puede tener un usuario)
     */
    public function user()
    {
        return $this->hasOne(User::class, 'funcionario_id', 'id');
    }

    /**
     * Relación con hojas de ruta como solicitante
     */
    public function hojasRutaSolicitante()
    {
        return $this->hasMany(HojaRuta::class, 'solicitante_id', 'id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('funcionario')
            ->logOnly(['unidad_id', 'ci', 'nombre', 'cargo', 'nro_item', 'estado', 'celular'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $authUser = auth()->user();
                $authUserId = $authUser?->id;
                $authUserName = $authUser?->name;

                $relatedUser = $this->user;
                $relatedUserId = $relatedUser?->id;
                $relatedUserUsername = $relatedUser?->usuario;
                $relatedFuncionarioName = $relatedUser?->funcionario?->nombre ?? $this->nombre;

                return "Funcionario: {$relatedFuncionarioName} | Usuario relacionado: {$relatedUserUsername} ({$relatedUserId}) | Evento: {$eventName} | Realizado por: " .
                    ($authUser ? "{$authUserName} ({$authUserId})" : "null");
            });
    }


}
