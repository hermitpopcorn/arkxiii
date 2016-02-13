<?php

use Illuminate\Database\Seeder;
use App\Jurusan;
use App\Kelas;

class DataSiswaTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker\Factory::create('id_ID');

        for ($i = 1; $i <= 120; ++$i) {
            $kelas = 1;
            if ($i > 30) {
                $kelas = 2;
            }
            if ($i > 60) {
                $kelas = 3;
            }
            if ($i > 90) {
                $kelas = 4;
            }

            $gender = $faker->randomElement(array('male', 'female'));
            $jk = ($gender == 'male') ? 'L' : 'P';

            $alamat = $faker->address;

            $kota = $faker->city;

            $kelas_menerima = 'X '.Jurusan::find(Kelas::find($kelas)->id_jurusan)->lengkap.' '.Kelas::find($kelas)->kelas;

            App\Siswa::create([
                'nama' => strtoupper($faker->firstName($gender).' '.$faker->lastName),
                'nis' => ($kelas <= 2 ? '14' : '15').str_pad($i, 7, '0', STR_PAD_LEFT),
                'nisn' => '999'.str_pad($i, 7, '0', STR_PAD_LEFT),
                'tempat_lahir' => strtoupper($kota),
                'tanggal_lahir' => $faker->date('Y-m-d', '1999-12-31'),
                'jenis_kelamin' => $jk,
                'agama' => 'Islam',
                'anak_ke' => $faker->numberBetween(1, 4),
                'alamat_siswa' => $alamat,
                'nomor_telepon_rumah_siswa' => null,
                'sekolah_asal' => strtoupper('SMP Negeri '.$faker->numberBetween(1, 9).' '.$kota),
                'id_kelas' => $kelas,
                'diterima_di_kelas' => $kelas_menerima,
                'tanggal_diterima' => '2013-07-15',
                'nama_ayah' => strtoupper($faker->name('male')),
                'nama_ibu' => strtoupper($faker->name('female')),
                'alamat_orang_tua' => $alamat,
                'nomor_telepon_rumah_orang_tua' => null,
                'pekerjaan_ayah' => 'Wirausaha',
                'pekerjaan_ibu' => 'Ibu rumah tangga',
                'nama_wali' => null,
                'nomor_telepon_rumah_wali' => null,
                'pekerjaan_wali' => null,
            ]);
        }
    }
}
