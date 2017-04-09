<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lists', function (Blueprint $table) {
            $table->removeColumn('price_id');
            $table->removeColumn('max_order');
        });

        Schema::create('listOnPrice', function (Blueprint $table) {
            $table->unsignedInteger('list_id');
            $table->foreign('list_id')->references('id')
                ->on('lists')->onDelete('cascade');

            $table->unsignedInteger('price_id');
            $table->foreign('price_id')->references('id')
                ->on('prices')->onDelete('cascade');

            $table->unsignedInteger('max_order')->nullable()->default(1);

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
        //
    }
}
