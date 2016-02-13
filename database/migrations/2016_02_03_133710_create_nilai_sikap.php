<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNilaiSikap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_sikap', function (Blueprint $table) {
            $table->integer('id_siswa')->unsigned();
            $table->integer('id_semester')->unsigned();
            $table->text('sikap');
            
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
        Schema::drop('nilai_sikap');
    }
}
