<?php

namespace App\Mail;

use App\Models\Guichet;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GuichetEnded extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Guichet instance
     */

    public $guichet;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Guichet $guichet)
    {
        $this->guichet = $guichet;
        $this->subject("Cloture du guichet ".$guichet->name);
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $sum = $this->guichet->orders()->where('state', 'paid')->sum('price')/100;
        $count = $this->guichet->orders()->where('state', 'paid')->count();
        return $this->view('emails.guichet.created', [
            'guichet'=>$this->guichet,
            'sum'=>$sum,
            'count'=>$count
        ]);
    }
}
