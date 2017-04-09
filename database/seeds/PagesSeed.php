<?php

use Illuminate\Database\Seeder;

class PagesSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $default = [];
        foreach ($default as $line)
        {
            DB::table('pages')->insert($line);
        }
    }
}
