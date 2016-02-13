<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndikatorProyek extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indikator_proyek', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_proyek')->unsigned();
            $table->integer('no+parent')->unsigned();
            $table->integer('urutan');
            $table->string('nama');
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
        Schema::drop('indikator_proyek');
    }
}
