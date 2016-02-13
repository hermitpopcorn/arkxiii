<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNilaiSikapspiritual extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::create('nilai_sikapspiritual', function (Blueprint $table) {
            $table->increments('id');
            $table->date('tanggal');
            $table->integer('id_mengajar')->unsigned();
            $table->integer('id_guru')->unsigned();
            $table->text('catatan_perilaku');
            $table->string('butir_sikap');

            $table->timestamps();
        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*
        Schema::drop('nilai_sikapspiritual');
        */
    }
}
