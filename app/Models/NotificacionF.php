<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionF extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    /**
     * Atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'hoja_id',
        'funcionario_id',
        'tipo',
        'mensaje',
        'fecha',
        'leido',
    ];

    /**
     * Casteo de atributos a tipos nativos.
     */
    protected $casts = [
        'fecha' => 'datetime',
        'leido' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con la hoja de ruta
     */
    public function hojaRuta()
    {
        return $this->belongsTo(HojaRuta::class, 'hoja_id', 'id');
    }

    /**
     * Relación con el funcionario receptor
     */
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id', 'id');
    }
}
