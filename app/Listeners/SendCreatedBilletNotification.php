<?php

namespace App\Listeners;

use App\Events\BilletCreated;
use App\Events\BilletUpdated;
use App\Mail\BilletEmited;
use Illuminate\Notifications\Notifiable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendCreatedBilletNotification implements ShouldQueue
{
    use Notifiable;

    protected $events = [
        'created' => BilletCreated::class,
        'updated' => BilletUpdated::class,
    ];
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
     * @param  BilletCreated  $event
     * @return void
     */
    public function handle(BilletCreated $event)
    {
        if($event->billet->price()->first()->sendBillet)
            Mail::to($event->billet->mail)->queue(new BilletEmited($event->billet));

    }
}
