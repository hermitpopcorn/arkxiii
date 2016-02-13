<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsensiTu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absensi_tu', function (Blueprint $table) {
            $table->integer('id_siswa')->unsigned();
            $table->integer('id_semester')->unsigned();
            $table->integer('sakit')->unsigned()->nullable();
            $table->integer('izin')->unsigned()->nullable();
            $table->integer('alpa')->unsigned()->nullable();
            
            $table->timestamps();
            
            $table->unique(['id_siswa', 'id_semester']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('absensi_tu');
    }
}
