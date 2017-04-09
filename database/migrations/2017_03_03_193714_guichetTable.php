<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GuichetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guichets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('mail')->nullable();
            $table->uuid('uuid');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->text('acl')->nullable();
            $table->timestamps();
            $table->index('uuid');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('guichet_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guichet', function (Blueprint $table) {
            //
        });
    }
}
