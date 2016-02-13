<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNilaiPrestasi extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('nilai_prestasi', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_siswa')->unsigned(); // siswa(id)
            $table->integer('id_semester')->unsigned(); // semester(id)
            $table->string('prestasi');
            $table->string('keterangan');
            $table->timestamps();

            $table->unique(['id_siswa', 'id_semester', 'prestasi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('nilai_prestasi');
    }
}
