<?php

namespace App\Mail;

use App\Models\Billet;
use App\Models\MailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\BilletParsing;
use Illuminate\Support\Facades\Log;

class BilletUpdated extends Mailable implements ShouldQueue
{

    use Queueable, SerializesModels, BilletParsing, InteractsWithQueue;

    /**
     * Billet model
     */

    protected $billet;
    protected $template_name = 'billet.updated';
    protected $template;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Billet $billet)
    {
        $this->billet = $billet;
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
        $billet_url = url()->route('download_billet', ['billet'=>$this->billet, 'securite'=>$this->billet->getDownloadSecurity()]);
        $mail_data = [
            'name' => $this->billet->name,
            'surname' => $this->billet->surname,
            'billet-name' => $this->billet->price->name,
            'billet-qrcode' => '<div style="text-align: center">
                                    <img src="data:image/png;base64,' . $this->billet->base64QrCode() . '" alt="eBillet" height="189px" width="189px" />
                                    <p style="font-family: Consolas, Menlo, Monaco, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, serif;text-align:center;text-decoration:none;margin-top:10px; color:#000000">'.$this->billet->getQrCodeSecurity().'</p>
                                    <p><a href="'.$billet_url.'"
style="background-color:#F71030;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:189px;margin-top:-10px;-webkit-text-size-adjust:none;">Télécharger le billet</a></p>
                                </div>',
        ];

        $this->subject($this->parseText($this->template->title, $mail_data));
        $content = $this->parseTextFromMarkdown($this->template->content, $mail_data);

        $billet = $this->billet;
        $this->withSwiftMessage(function (\Swift_Message $message) use ($billet) {
            $message->billet_id = $billet->id;
        });

        // On n'envoi plus de pdf => téléchargement + QrCode
        return $this->view('emails.emails')
            ->with([
                'content'=>$content,
                'subject' => $this->subject
            ]);
    }
}
