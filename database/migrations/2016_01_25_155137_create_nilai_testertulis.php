<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNilaiTestertulis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_testertulis', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_mengajar')->unsigned();
            $table->integer('id_indikator')->unsigned();
            $table->date('tanggal');
            $table->text('nama_tes');

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
        Schema::drop('nilai_testertulis');
    }
}
