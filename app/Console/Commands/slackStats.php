<?php

namespace App\Console\Commands;

use App\Jobs\slackAlert;
use App\Models\Billet;
use App\Models\Price;
use Illuminate\Console\Command;
use Maknz\Slack\Facades\Slack;

class slackStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slack:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send stats using slack';

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
        dispatch(new slackAlert());
    }
}
