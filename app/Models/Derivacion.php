<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Derivacion extends Model
{
    use HasFactory;

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
     * Relación con Hoja de Ruta
     */
    public function hojaRuta()
    {
        return $this->belongsTo(HojaRuta::class, 'hoja_id');
    }

    /**
     * Unidad de origen
     */
    public function unidadOrigen()
    {
        return $this->belongsTo(Unidad::class, 'unidad_origen_id');
    }

    /**
     * Unidad destino
     */
    public function unidadDestino()
    {
        return $this->belongsTo(Unidad::class, 'unidad_destino_id');
    }

    /**
     * Funcionario asociado a la derivación (opcional)
     */
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }
}
