<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNilaiProses extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('nilai_proses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_mengajar')->unsigned();
            $table->integer('id_indikator')->unsigned();
            $table->date('tanggal');
            $table->string('nama_proses', 100);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('nilai_proses');
    }
}
