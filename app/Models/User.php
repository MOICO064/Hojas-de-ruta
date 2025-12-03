<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    // Tabla explícita (opcional, Laravel asume "users")
    protected $table = 'users';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'email',
        'password',
        'email_verified_at',
        'funcionario_id',
        'estado'
    ];

    /**
     * Los atributos que deben estar ocultos para arrays/JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deben ser casteados a tipos nativos.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id', 'id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('usuario')
            ->logOnly(['name', 'email', 'funcionario_id'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $authUser = auth()->user();
                $authUserId = $authUser?->id;
                $authUserName = $authUser?->email;

                $relatedUser = $this;
                $relatedUserId = $relatedUser?->id;
                $relatedUserName = $relatedUser?->email;

                return "Usuario: {$relatedUserName} | "
                    . "Evento: {$eventName} | "
                    . "Realizado por: " . ($authUser ? "{$authUserName} ({$authUserId})" : "Sistema/Desconocido");
            });
    }
}
