<?php

use Illuminate\Database\Seeder;

class DataKelasTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        App\Kelas::create([
            'tingkat' => 2,
            'id_jurusan' => 1,
            'kelas' => 'A',
            'angkatan' => '41',
        ]);

        App\Kelas::create([
            'tingkat' => 2,
            'id_jurusan' => 1,
            'kelas' => 'B',
            'angkatan' => '41',
        ]);

        App\Kelas::create([
            'tingkat' => 1,
            'id_jurusan' => 1,
            'kelas' => 'A',
            'angkatan' => '42',
        ]);

        App\Kelas::create([
            'tingkat' => 1,
            'id_jurusan' => 1,
            'kelas' => 'B',
            'angkatan' => '42',
        ]);
    }
}
