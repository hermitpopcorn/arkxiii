<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKelas extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('tingkat')->unsigned();
            $table->integer('id_jurusan')->unsigned(); // -> jurusan(id)
            $table->char('kelas', 1);
            $table->integer('angkatan')->unsigned();
            $table->timestamps();

            $table->unique(array('tingkat', 'id_jurusan', 'kelas', 'angkatan'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('kelas');
    }
}
