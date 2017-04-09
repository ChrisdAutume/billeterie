<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('price_id')->unsigned();
            $table->foreign('price_id')->references('id')
                ->on('prices')->onDelete('cascade');

            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['input', 'text', 'checkbox']);
            $table->text('default')->nullable();
            $table->boolean('mandatory')->default(false);
            $table->timestamps();

        });

        Schema::table('billets', function (Blueprint $table) {
            $table->longText('fields')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
