<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price');
            $table->unsignedInteger('max_order')->default(0);
            $table->unsignedInteger('min_choice')->default(1);
            $table->unsignedInteger('max_choice')->default(0);
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->boolean('isMandatory')->default(false);
            $table->timestamps();
        });

        Schema::create('billet_option', function (Blueprint $table) {
            $table->unsignedInteger('billet_id');
            $table->unsignedInteger('option_id');
            $table->unsignedInteger('qty');
            $table->integer('amount');
            $table->timestamps();

            $table->primary(['billet_id', 'option_id']);
        });

        Schema::create('price_option', function (Blueprint $table) {
            $table->unsignedInteger('price_id');
            $table->unsignedInteger('option_id');
            $table->timestamps();

            $table->primary(['price_id', 'option_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options');
        Schema::dropIfExists('billet_option');
        Schema::dropIfExists('price_option');
    }
}
