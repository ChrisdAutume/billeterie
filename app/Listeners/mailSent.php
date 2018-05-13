<?php

namespace App\Listeners;

use App\Models\Billet;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class mailSent
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
     * @param  MessageSent  $event
     * @return void
     */
    public function handle(MessageSent $event)
    {
        if(isset($event->message->billet_id))
        {
            $billet = Billet::find($event->message->billet_id);
            $billet->sent_at = new \DateTime();
            $billet->save();
        }
    }
}
