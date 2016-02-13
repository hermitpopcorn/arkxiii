<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNilaiEkstrakurikuler extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('nilai_ekstrakurikuler', function (Blueprint $table) {
            $table->integer('id_siswa')->unsigned(); // siswa(id)
            $table->string('ekstrakurikuler');
            $table->integer('id_semester')->unsigned(); // semester(id)
            $table->text('nilai');
            $table->timestamps();

            $table->unique(['id_siswa', 'ekstrakurikuler', 'id_semester'], 'uniqueIdentifierTrio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('nilai_ekstrakurikuler');
    }
}
