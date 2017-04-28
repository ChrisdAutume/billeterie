<?php

namespace App\Listeners;

use App\Events\BilletUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendUpdatedBilletNotification implements ShouldQueue
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
     * @param  BilletUpdated  $event
     * @return void
     */
    public function handle(BilletUpdated $event)
    {
        if(!$event->billet->validated_at)
        {
            if($event->billet->price()->first()->sendBillet)
                Mail::to($event->billet->mail)->queue(new \App\Mail\BilletUpdated($event->billet));
        }
    }
}
