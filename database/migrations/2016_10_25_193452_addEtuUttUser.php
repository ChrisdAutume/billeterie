<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEtuUttUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('student_id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('mail');
            $table->string('etuutt_access_token')->nullable();
            $table->string('etuutt_refresh_token')->nullable();
            $table->integer('level')->default(0);
            $table->dateTime('last_login');

            $table->dropColumn('password');
            $table->dropColumn('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'firstname',
                'lastname',
                'mail',
                'etuutt_access_token',
                'etuutt_refresh_token',
                'level',
                'last_login',
                'student_id'
            ]);

            $table->string('password');
        });
    }
}
