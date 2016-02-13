<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiswa extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_kelas')->unsigned()->nullable(); // -> kelas(id)uk
            $table->string('nama');
            $table->char('nisn', 10)->unique();
            $table->char('nis', 9)->unique();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('agama');
            $table->enum('status_dalam_keluarga', ['kandung', 'angkat'])->default('kandung');
            $table->integer('anak_ke')->unsigned()->nullable();
            $table->string('alamat_siswa');
            $table->string('nomor_telepon_rumah_siswa')->nullable();
            $table->string('sekolah_asal');
            $table->string('diterima_di_kelas');
            $table->date('tanggal_diterima');
            $table->string('nama_ayah');
            $table->string('nama_ibu');
            $table->string('alamat_orang_tua');
            $table->string('nomor_telepon_rumah_orang_tua')->nullable();
            $table->string('pekerjaan_ayah');
            $table->string('pekerjaan_ibu');
            $table->string('nama_wali')->nullable();
            $table->string('alamat_wali')->nullable();
            $table->string('nomor_telepon_rumah_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('siswa');
    }
}
