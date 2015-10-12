<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account', 32)->nullable();
            $table->string('email', 32)->nullable();
            $table->string('cellphone', 11);
            $table->string('password', 64);
            $table->string('salt', 16);
            $table->integer('level_id');
            $table->dateTime('last_login_time');
            $table->string('last_login_ip');
            $table->timestamps();

            $table->unique('cellphone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
