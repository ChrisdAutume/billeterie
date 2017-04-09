<?php

namespace App\Jobs;

use App\Models\Guichet;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maknz\Slack\Facades\Slack;
use App\Models\Price;

class slackAlert implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $hour = intval(date('H'));
        $summary = "";
        if( $hour>=9 && $hour<=23)
        {
            if($hour==9)
                $summary .= "Bonjour, passez une bonne journée :p \n \n";
            $summary .= "Mise à jour: \n";
            foreach (Price::all() as $price) {
                $summary .= "   - ".$price->name . " " . $price->billets()->count() . " vendu(s) \n";
            }

            if(Guichet::count()>0) {
                $summary .= "\n #Guichets: \n";
                foreach (Guichet::all() as $guichet) {
                    $summary .= " -".$guichet->name." ".$guichet->billets()->count()." billet(s) vendu(s) \n";
                }
            }

            if($hour==23)
                $summary .= "Bonne nuit et a demain =p \n";

            Slack::send($summary);
        }

        //renew slack alert
        $job = (new slackAlert())
            ->delay(Carbon::now()->addHour(2));
        dispatch($job);
    }
}
