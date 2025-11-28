<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unidad extends Model
{
    use HasFactory;

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
     * Unidad padre (para estructura jerárquica).
     */
    public function unidadPadre()
    {
        return $this->belongsTo(Unidad::class, 'unidad_padre_id');
    }

    /**
     * Unidades hijas (dependencias).
     */
    public function unidadesHijas()
    {
        return $this->hasMany(Unidad::class, 'unidad_padre_id');
    }

    /**
     * Funcionarios asociados a esta unidad.
     */
    public function funcionarios()
    {
        return $this->hasMany(Funcionario::class, 'unidad_id');
    }

    /**
     * Hojas de ruta emitidas desde esta unidad.
     */
    public function hojasRutaOrigen()
    {
        return $this->hasMany(HojaRuta::class, 'unidad_origen_id');
    }
}
