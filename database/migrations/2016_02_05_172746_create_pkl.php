<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePkl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkl', function (Blueprint $table) {
            $table->integer('id_siswa')->unsigned(); // siswa(id)
            $table->integer('id_semester')->unsigned(); // semester(id)
            $table->string('mitra');
            $table->string('lokasi');
            $table->tinyInteger('lama');
            $table->string('keterangan');
            $table->timestamps();

            $table->unique(['id_siswa', 'id_semester', 'mitra', 'lokasi'], 'uniqueIdentifierPkl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('pkl');
    }
}
