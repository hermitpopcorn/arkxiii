<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndikator extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('indikator', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_mapel')->unsigned(); // mapel(id)
            $table->tinyInteger('tingkat'); // 1, 2, atau 3
            $table->string('kode_indikator', 10);
            $table->string('nama');
            $table->integer('id_semester')->unsigned();
            $table->string('deskripsi');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('indikator');
    }
}
