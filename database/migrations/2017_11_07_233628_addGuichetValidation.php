<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGuichetValidation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guichets', function (Blueprint $table) {
            $table->longText('acl')->change();
            $table->enum('type', ['sell', 'validation'])->default('sell')->after('name');
        });

        Schema::create('billet_guichet', function (Blueprint $table) {
            $table->unsignedInteger('billet_id');
            $table->unsignedInteger('guichet_id');
            $table->enum('answer', [
                'success',
                'already_validated',
                'wrong_checksum',
                ]);
            $table->timestamps();

            $table->primary(['billet_id', 'guichet_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guichets', function (Blueprint $table) {
            $table->text('acl')->change();
            $table->removeColumn('type');
        });

        Schema::dropIfExists('billet_guichet');
    }
}
