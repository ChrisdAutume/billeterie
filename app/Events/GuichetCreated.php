<?php

namespace App\Events;

use App\Models\Guichet;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GuichetCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $guichet;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Guichet $guichet)
    {
        $this->guichet = $guichet;
    }
}
