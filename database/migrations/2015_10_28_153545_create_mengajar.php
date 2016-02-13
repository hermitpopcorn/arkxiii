<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMengajar extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('mengajar', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_guru')->unsigned(); // guru(id)
            $table->integer('id_kelas')->unsigned(); // kelas(id)
            $table->integer('id_mapel')->unsigned(); // mapel(id)
            $table->integer('id_semester')->unsigned(); // semester(id)

            $table->timestamps();

            $table->unique(array('id_guru', 'id_kelas', 'id_mapel', 'id_semester'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('mengajar');
    }
}
