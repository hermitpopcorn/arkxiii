<?php

use Illuminate\Database\Seeder;

class PengaturanDefault extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Pengaturan::insert([
            'key' => 'nama_sekolah',
            'value' => 'SMK Negeri 1 Cimahi'
        ]);
        App\Pengaturan::insert([
            'key' => 'npsn',
            'value' => '20224136'
        ]);
        App\Pengaturan::insert([
            'key' => 'nss',
            'value' => '321020903003P'
        ]);
        App\Pengaturan::insert([
            'key' => 'alamat_sekolah',
            'value' => "Jl. Mahar Martanegara No.48\nKode Pos 40553 Telp. 022-6629683"
        ]);
        App\Pengaturan::insert([
            'key' => 'kelurahan',
            'value' => 'Utama'
        ]);
        App\Pengaturan::insert([
            'key' => 'kecamatan',
            'value' => 'Cimahi Selatan'
        ]);
        App\Pengaturan::insert([
            'key' => 'kabupaten',
            'value' => 'Cimahi'
        ]);
        App\Pengaturan::insert([
            'key' => 'provinsi',
            'value' => 'Jawa Barat'
        ]);
        App\Pengaturan::insert([
            'key' => 'website',
            'value' => 'www.smkn1-cmi.sch.id'
        ]);
        App\Pengaturan::insert([
            'key' => 'email',
            'value' => 'smkn1cmi@bdg.centrin.net.id'
        ]);
        App\Pengaturan::insert([
            'key' => 'kepala_sekolah.nama',
            'value' => 'Drs. Ermizul, M.Pd.'
        ]);
        App\Pengaturan::insert([
            'key' => 'kepala_sekolah.nip',
            'value' => '195711011982031024'
        ]);
        App\Pengaturan::insert([
            'key' => 'nilai_pengetahuan.model',
            'value' => '2'
        ]);
        App\Pengaturan::insert([
            'key' => 'nilai_pengetahuan.bobot_skor_kd',
            'value' => '1:3'
        ]);
        App\Pengaturan::insert([
            'key' => 'nilai_penegtahuan.bobot_nilai_rapor',
            'value' => '2:1:1'
        ]);
        App\Pengaturan::insert([
            'key' => 'nilai_keterampilan.bobot_skor_kd',
            'value' => '1:1:1'
        ]);
    }
}
