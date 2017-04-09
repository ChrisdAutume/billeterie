<?php

namespace App\Jobs;

use App\Models\Guichet as GuichetModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GuichetEnded implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Guichet model
     */
    public $guichet;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(GuichetModel $guichet=null)
    {
        $this->guichet = $guichet;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        if(!is_null($this->guichet->mail))
            $mailer->to($this->guichet->mail)
                ->cc(config('billeterie.contact'))
                ->queue(new \App\Mail\GuichetEnded($this->guichet));
    }
}
