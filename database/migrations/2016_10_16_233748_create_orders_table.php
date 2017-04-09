<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('mean_of_paiment',['check', 'cash', 'buckutt', 'cb', 'online'])->nullable();
            $table->string('transaction_id')->nullable();
            $table->integer('price')->default(0);
            $table->enum('state', ['ordering', 'paid', 'canceled', 'refunded'])->default('ordering');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
