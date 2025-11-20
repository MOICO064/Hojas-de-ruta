<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Derivacion extends Model
{
    use HasFactory;

    protected $table = 'derivaciones';

    /**
     * Atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'hoja_id',
        'unidad_origen_id',
        'unidad_destino_id',
        'descripcion',
        'estado',
        'derivado_por',
        'fecha_derivacion',
        'fecha_recepcion',
    ];

    /**
     * Casteo de atributos a tipos nativos.
     */
    protected $casts = [
        'fecha_derivacion' => 'datetime',
        'fecha_recepcion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con hoja de ruta
     */
    public function hojaRuta()
    {
        return $this->belongsTo(HojaRuta::class, 'hoja_id', 'id');
    }

    /**
     * Unidad de origen de la derivación
     */
    public function unidadOrigen()
    {
        return $this->belongsTo(Unidad::class, 'unidad_origen_id', 'id');
    }

    /**
     * Unidad destino de la derivación
     */
    public function unidadDestino()
    {
        return $this->belongsTo(Unidad::class, 'unidad_destino_id', 'id');
    }

    /**
     * Funcionario que derivó
     */
    public function derivadoPor()
    {
        return $this->belongsTo(Funcionario::class, 'derivado_por', 'id');
    }
}
