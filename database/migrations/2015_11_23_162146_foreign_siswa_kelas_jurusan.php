<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignSiswaKelasJurusan extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        /*
        Schema::table('kelas', function (Blueprint $table) {
           $table->foreign('jurusan')->references('id')->on('jurusan');
        });

        Schema::table('siswa', function (Blueprint $table) {
           $table->foreign('kelas')->references('id')->on('kelas');
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        /*
        Schema::table('kelas', function (Blueprint $table) {
           $table->dropForeign('kelas_jurusan_foreign');
        });

        Schema::table('siswa', function (Blueprint $table) {
           $table->dropForeign('siswa_kelas_foreign');
        });
        */
    }
}
