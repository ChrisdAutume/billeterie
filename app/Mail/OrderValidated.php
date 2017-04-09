<?php

namespace App\Mail;

use App\Models\MailTemplate;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\BilletParsing;

class OrderValidated extends Mailable implements ShouldQueue
{

    use Queueable, SerializesModels, BilletParsing, InteractsWithQueue;

    /**
     * Order model
     */

    protected $order;
    protected $template_name = 'order.validated';
    protected $template;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->template = MailTemplate::where('name',$this->template_name)->first();
        if(!$this->template || !$this->template->isActive)
        {
            $this->delete();
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $summary = '<ul>';
        foreach ($this->order->billets()->get() as $billet)
        {
            $summary .= '<li>'.$billet->price->name.' pour <strong>'.$billet->name.' '.$billet->surname.' </strong></li>';
        }
        $summary .= '</ul>';

        $mail_data = [
            'order-surname' => $this->order->surname,
            'order-name' => $this->order->name,
            'order-summary' => $summary,
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
