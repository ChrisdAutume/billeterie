<?php

namespace App\Mail;

use App\Models\Guichet;
use App\Models\MailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\BilletParsing;

class GuichetCreated extends Mailable implements ShouldQueue
{

    use Queueable, SerializesModels, BilletParsing, InteractsWithQueue;

    /**
     * Billet model
     */

    protected $guichet;
    protected $template_name = 'guichet.created';
    protected $template;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Guichet $guichet)
    {
        $this->guichet = $guichet;
        $this->template = MailTemplate::where('name',$this->template_name)->first();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(!$this->template || !$this->template->isActive)
        {
            $this->delete();
            return true;
        }

        $mail_data = [
            'guichet-name'=> $this->guichet->name,
            'guichet-start-at'=> $this->guichet->start_at,
            'guichet-end-at' => $this->guichet->end_at,
            'guichet-link' => url()->route('get_sell_guichet', ['uuid'=> $this->guichet->uuid]),
            'contact' => config('billeterie.contact'),
        ];

        $this->subject($this->parseText($this->template->title, $mail_data));
        $content = $this->parseTextFromMarkdown($this->template->content, $mail_data);

        return $this->view('emails.emails')
            ->with([
                'content'=>$content,
                'subject' => $this->subject
            ]);
    }

}
