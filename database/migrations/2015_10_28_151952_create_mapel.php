<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMapel extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('mapel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama')->unique();
            $table->string('singkat', 8);
            $table->enum('kelompok', ['A', 'B', 'C1', 'C2', 'C3', 'WK']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('mapel');
    }
}
