<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_lists', function (Blueprint $table) {
            $table->integer('list_id')->unsigned();
            $table->string('value');
            $table->timestamps();

            $table->primary(['list_id', 'value']);

        });
        /*
        Schema::table('item_lists', function (Blueprint $table) {
            $table->foreign('list_id')
                ->references('id')->on('lists')
                ->onDelete('cascade');

        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_lists');
    }
}
