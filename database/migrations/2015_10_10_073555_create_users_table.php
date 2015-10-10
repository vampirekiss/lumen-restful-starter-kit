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
            $table->string('account')->nullable();
            $table->string('email')->nullable();
            $table->string('cellphone');
            $table->string('password');
            $table->string('salt');
            $table->dateTime('last_login_time');
            $table->string('last_login_ip');
            $table->timestamps();

            $table->unique(['account', 'email', 'cellphone']);
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
