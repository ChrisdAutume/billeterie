<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\BilletCreated' => [
            'App\Listeners\SendCreatedBilletNotification',
        ],
        'App\Events\BilletUpdated' => [
            'App\Listeners\SendUpdatedBilletNotification',
        ],
        'App\Events\OrderUpdated' => [
            'App\Listeners\SendOrderNotification',
        ],
        'App\Events\GuichetCreated' => [
            'App\Listeners\SendCreatedGuichetNotification',
        ],
        'Illuminate\Mail\Events\MessageSent' => [
            'App\Listeners\mailSent',
        ],


    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
