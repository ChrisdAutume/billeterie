<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBilletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billets', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->string('name');
            $table->string('surname');
            $table->string('mail');
            $table->integer('order_id')->unsigned();
            $table->integer('price_id')->unsigned();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();


        });

        Schema::table('billets', function (Blueprint $table){
            //$table->foreign('order_id')
            //->references('id')->on('orders')
            //    ->onDelete('cascade');

            //$table->foreign('price_id')
            //    ->references('id')->on('prices')
            //    ->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billets');
    }
}
