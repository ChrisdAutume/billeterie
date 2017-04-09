<?php

namespace App\Listeners;

use App\Events\GuichetCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCreatedGuichetNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  GuichetCreated  $event
     * @return void
     */
    public function handle(GuichetCreated $event)
    {
        //
    }
}
