<?php

namespace App\Listeners;

use App\Events\OrderUpdated;
use App\Mail\OrderRefused;
use App\Mail\OrderValidated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderNotification
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
     * @param  OrderUpdated  $event
     * @return void
     */
    public function handle(OrderUpdated $event)
    {
        // Check if state updated
        if($event->order->isDirty('state'))
        {
            if($event->order->state == 'paid')
                Mail::to($event->order->mail)->queue(new OrderValidated($event->order));
            if($event->order->state == 'canceled')
                Mail::to($event->order->mail)->queue(new OrderRefused($event->order));
        }
    }
}
