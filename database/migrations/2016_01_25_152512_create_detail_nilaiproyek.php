<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailNilaiproyek extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_nilaiproyek', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_proyek')->unsigned();
            $table->integer('id_siswa')->unsigned();
            $table->tinyInteger('parent')->unsigned();
            $table->tinyInteger('urutan')->unsigned();
            $table->tinyInteger('nilai')->unsigned();
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
        Schema::drop('detail_nilaiproyek');
    }
}
