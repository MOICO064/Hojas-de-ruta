<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anulacion extends Model
{
    use HasFactory;

    protected $table = 'anulaciones';
    protected $fillable = [
        'hoja_id',
        'derivacion_id',
        'funcionario_id',
        'justificacion',
        'fecha_anulacion',
    ];

    protected $casts = [
        'fecha_anulacion' => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    public function hojaRuta()
    {
        return $this->belongsTo(HojaRuta::class, 'hoja_id');
    }

    public function derivacion()
    {
        return $this->belongsTo(Derivacion::class, 'derivacion_id');
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }
}