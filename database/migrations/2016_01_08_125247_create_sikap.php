<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSikap extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sikap', function (Blueprint $table) {
           $table->integer('id_siswa')->unsigned(); // siswa(id)
           $table->integer('id_semester')->unsigned(); // semester(id)
           $table->text('deskripsi');

           $table->timestamps();
           
           $table->unique(['id_siswa', 'id_semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('sikap');
    }
}
