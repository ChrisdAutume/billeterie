<?php

use Illuminate\Database\Seeder;

class MailTemplateSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $default = [
            [
                'name' => 'billet.emited',
                'title' => "Confirmation d'obtention d'un billet",
                'content' => " "
            ],
            [
                'name' => 'guichet.created',
                'title' => "Confirmation d'obtention d'un billet",
                'content' => " "
            ],
            [
                'name' => 'guichet.ended',
                'title' => "Confirmation d'obtention d'un billet",
                'content' => " "
            ],
            [
                'name' => 'order.refused',
                'title' => "Confirmation d'obtention d'un billet",
                'content' => " "
            ],
            [
                'name' => 'order.validated',
                'title' => "Confirmation d'obtention d'un billet",
                'content' => ""
            ],

        ];
        foreach ($default as $line)
        {
            DB::table('mail_templates')->insert($line);
        }
    }
}
