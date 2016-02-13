<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuru extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->increments('id');
            $table->char('nip', 18)->nullable();
            $table->string('nama');
            $table->string('username', 16)->unique();
            $table->char('password', 60);
            $table->tinyInteger('staf')->unsigned()->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('guru');
    }
}
