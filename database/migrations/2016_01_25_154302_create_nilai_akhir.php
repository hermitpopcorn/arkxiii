<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNilaiAkhir extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('nilai_akhir', function (Blueprint $table) {
            $table->integer('id_siswa')->unsigned();
            $table->integer('id_mapel')->unsigned();
            $table->integer('id_semester')->unsigned();
            $table->tinyInteger('nilai_pengetahuan')->unsigned()->nullable();
            $table->string('deskripsi_pengetahuan', 255)->nullable();
            $table->tinyInteger('nilai_keterampilan')->unsigned()->nullable();
            $table->string('deskripsi_keterampilan', 255)->nullable();

            $table->timestamps();
            
            $table->unique(['id_siswa', 'id_mapel', 'id_semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('nilai_akhir');
    }
}
