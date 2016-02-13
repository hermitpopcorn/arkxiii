<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNilaiUasuts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_uasuts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_mengajar')->unsigned();
            $table->integer('id_siswa')->unsigned();
            $table->tinyInteger('uas')->unsigned();
            $table->tinyInteger('uts')->unsigned();

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
        Schema::drop('nilai_uasuts');
    }
}
