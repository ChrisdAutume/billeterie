<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameListOnPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('listOnPrice');
        Schema::create('liste_price', function (Blueprint $table) {
            $table->unsignedInteger('liste_id');
            $table->foreign('liste_id')->references('id')
                ->on('lists')->onDelete('cascade');

            $table->unsignedInteger('price_id');
            $table->foreign('price_id')->references('id')
                ->on('prices')->onDelete('cascade');

            $table->unsignedInteger('max_order')->nullable()->default(1);

            $table->timestamps();
        });
        Schema::table('lists', function (Blueprint $table) {
            $table->dropColumn('price_id');
            $table->dropColumn('max_order');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
