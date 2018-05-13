<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Billet;

class sendTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing:tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoi de tickets non envoyés';

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
        $this->info('## Procédure d\' envoi des billets non déja expédié');
        $billets = Billet::whereNull('sent_at')->get();

        $this->info('Nombre de billets en attente : '.count($billets));
        $this->info('Envoi en cours ...');

        foreach ($billets as $billet)
        {
            $this->info('- Billet n°'.$billet->uuid.' à '.$billet->mail);
            $billet->sendToMail();
        }
        $this->info('Phase d\'envoi finalisé');
    }
}
