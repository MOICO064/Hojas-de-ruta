<?php

namespace App\Events;

use App\Models\NotificacionF;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class NuevaNotificacion implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $notificacion;

    public function __construct(NotificacionF $notificacion)
    {
        $this->notificacion = $notificacion;
    }

    public function broadcastOn()
    {
        return new Channel('funcionario.' . $this->notificacion->funcionario_id);
    }

    public function broadcastAs()
    {
        return 'nueva-notificacion';
    }
}