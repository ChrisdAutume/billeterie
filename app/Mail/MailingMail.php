<?php

namespace App\Mail;

use App\Models\Billet;
use App\Models\Item_list;
use App\Models\MailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\BilletParsing;

class MailingMail extends Mailable implements ShouldQueue
{

    use Queueable, SerializesModels, InteractsWithQueue;

    /**
     * Billet model
     */

    protected $item;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sujet)
    {
        $this->subject($sujet);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.mailing');
    }
}
