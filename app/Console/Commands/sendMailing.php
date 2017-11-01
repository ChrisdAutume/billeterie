<?php

namespace App\Console\Commands;

use App\Mail\MailingMail;
use App\Models\Liste;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class sendMailing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Permet l'envoi d'un mailing de masse";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Merci de répondre aux questions suivantes:');
        $this->info('Choix de la liste d\'envoi: ');
        $this->table(['id','name'], Liste::where('type', "MAILIST")->get(['id','name']));
        $list_id = $this->ask('Liste ID: ');

        $list = Liste::find($list_id);
        if(!$list)
        {
            $this->error("No list selected !");
            exit();
        }
        $sujet = $this->ask('Sujet du mail: ');

        $this->info("Envoi prévu de ".count($list->itemList)." mails.");
        if ($this->confirm('Valider l\'envoi ? [y|N]')) {
            $bar = $this->output->createProgressBar(count($list->itemList));
            foreach ($list->itemList as $email)
            {
                try
                {
                    Mail::to($email->value)
                        ->queue(new MailingMail($sujet));

                }catch (\Exception $e)
                {
                    $this->error('Error sending to '.$email->value);
                }
                $bar->advance();
            }
            $bar->finish();
        }
    }
}
