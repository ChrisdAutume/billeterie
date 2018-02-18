<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class setAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:set-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Set an user as superadmin";

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
        $this->info('This command will set an user as superadmin.');
        $this->info('Note: The target user has to logged in once first.');
        $mail = $this->ask('UTT email: ');

        $user = User::where(['mail' => $mail])->first();
        if(!$user)
        {
            $this->error("User not found ! Ask him to go to the website and authenticate.");
            exit();
        }
        $user->level = User::ROLE_SUPERADMIN;
        $user->save();

        $this->info($user->name . ' is now superadmin !');
    }
}
