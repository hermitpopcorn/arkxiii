<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailTestertulis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_testertulis', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_testertulis')->unsigned();
            $table->integer('id_siswa')->unsigned();
            $table->tinyInteger('nilai');
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
        Schema::drop('detail_testertulis');
    }
}
