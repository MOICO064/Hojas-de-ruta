<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secretaria extends Model
{
    use HasFactory;

    // Tabla explícita (opcional, Laravel asume "secretarias")
    protected $table = 'secretarias';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'codigo',
        'nombre',
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
     * Relación con funcionarios (una secretaria tiene muchos funcionarios)
     */
    public function funcionarios()
    {
        return $this->hasMany(Funcionario::class, 'secretaria_id', 'id');
    }
}
