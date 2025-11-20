<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use HasFactory;

    // Tabla explícita
    protected $table = 'unidades';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'secretaria_id',
        'nombre',
        'codigo',
        'telefono',
        'celular',
        'estado',
    ];

    /**
     * Los atributos que deben ser casteados a tipos nativos.
     */
    protected $casts = [
        'telefono' => 'integer',
        'celular' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con la secretaria (una unidad pertenece a una secretaria)
     */
    public function secretaria()
    {
        return $this->belongsTo(Secretaria::class, 'secretaria_id', 'id');
    }

    /**
     * Relación con funcionarios (una unidad tiene muchos funcionarios)
     */
    public function funcionarios()
    {
        return $this->hasMany(Funcionario::class, 'unidad_id', 'id');
    }

    /**
     * Relación con hojas de ruta (una unidad puede tener muchas hojas de ruta como origen)
     */
    public function hojasRutaOrigen()
    {
        return $this->hasMany(HojaRuta::class, 'unidad_origen', 'id');
    }

    /**
     * Relación con hojas de ruta (una unidad puede tener muchas hojas de ruta como destino)
     */
    public function hojasRutaDestino()
    {
        return $this->hasMany(HojaRuta::class, 'unidad_destino', 'id');
    }
}
