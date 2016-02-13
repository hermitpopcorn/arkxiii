<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKetuntasanBelajar extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ketuntasan_belajar', function (Blueprint $table) {
            $table->integer('id_mapel')->unsigned(); // pelajaran(id)
            $table->integer('id_semester')->unsigned(); // semester(id)
            $table->smallInteger('kb_tingkat_1p')->unsigned();
            $table->smallInteger('kb_tingkat_1k')->unsigned();
            $table->smallInteger('kb_tingkat_2p')->unsigned();
            $table->smallInteger('kb_tingkat_2k')->unsigned();
            $table->smallInteger('kb_tingkat_3p')->unsigned();
            $table->smallInteger('kb_tingkat_3k')->unsigned();
            $table->timestamps();

            $table->unique(['id_mapel', 'id_semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('ketuntasan_belajar');
    }
}
