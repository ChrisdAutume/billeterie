<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSentAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billets', function (Blueprint $table) {
            $table->dateTime('sent_at')->nullable();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dateTime('sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billets', function (Blueprint $table) {
            $table->removeColumn('sent_at');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->removeColumn('sent_at');
        });
    }
}
