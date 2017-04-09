<?php

namespace App\Mail;

use App\Models\Don;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DonReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Don model
     */
    public $don;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Don $don)
    {
        $this->don = $don;
        $this->subject('Le don de promo pris en compte.');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.don.accepted')->with(['don'=>$this->don]);
    }
}
