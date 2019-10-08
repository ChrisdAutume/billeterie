<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameFieldsAndAddChoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billets', function (Blueprint $table) {
            $table->renameColumn('fields', 'fields_data');
        });

        Schema::table('field_prices', function (Blueprint $table) {
            $table->json('values');
        });
        DB::statement("ALTER TABLE field_prices CHANGE COLUMN `type` `type` enum('input','text','checkbox', 'select') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billets', function (Blueprint $table) {
            $table->renameColumn('fields_data', 'fields');
        });
        Schema::table('field_prices', function (Blueprint $table) {
            $table->dropColumn('values');
        });
        DB::statement("ALTER TABLE field_prices CHANGE COLUMN `type` `type` enum('input','text','checkbox') ");
    }
}
