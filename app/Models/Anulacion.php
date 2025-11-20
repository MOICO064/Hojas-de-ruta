<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anulacion extends Model
{
    use HasFactory;

    protected $table = 'anulaciones';

    /**
     * Atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'hoja_id',
        'funcionario_id',
        'justificacion',
        'fecha_anulacion',
    ];

    /**
     * Casteo de atributos a tipos nativos.
     */
    protected $casts = [
        'fecha_anulacion' => 'datetime',
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
     * Relación con el funcionario que anuló
     */
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id', 'id');
    }
}
