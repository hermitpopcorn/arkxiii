<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSemester extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('semester', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('semester', [1, 2]);
            $table->char('tahun_ajaran', 11);
            $table->tinyInteger('aktif')->unsigned();
            $table->timestamps();

            $table->unique(array('semester', 'tahun_ajaran'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('semester');
    }
}
