<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HojaRuta extends Model
{
    use HasFactory;

    protected $table = 'hoja_ruta';

    /**
     * Atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'idgral',
        'numero_unidad',
        'unidad_origen_id',
        'solicitante_id',
        'fecha_creacion',
        'cite',
        'prioridad',
        'asunto',
        'estado',
        'gestion',
        'fojas',
        'creado_por',
        'fecha_impresion',
    ];

    /**
     * Casteo de atributos a tipos nativos.
     */
    protected $casts = [
        'fecha_creacion' => 'date',
        'fecha_impresion' => 'datetime',
        'fojas' => 'integer',
    ];

    /**
     * Unidad de origen de la hoja de ruta
     */
    public function unidadOrigen()
    {
        return $this->belongsTo(Unidad::class, 'unidad_origen_id', 'id');
    }

    /**
     * Solicitante (funcionario)
     */
    public function solicitante()
    {
        return $this->belongsTo(Funcionario::class, 'solicitante_id', 'id');
    }

    /**
     * Usuario/funcionario que creó la hoja de ruta
     */
    public function creador()
    {
        return $this->belongsTo(Funcionario::class, 'creado_por', 'id');
    }

    /**
     * Derivaciones de la hoja de ruta
     */
    public function derivaciones()
    {
        return $this->hasMany(Derivacion::class, 'idhoja', 'id');
    }

    /**
     * Anulaciones de la hoja de ruta
     */
    public function anulaciones()
    {
        return $this->hasMany(Anulacion::class, 'idhoja', 'id');
    }
}
