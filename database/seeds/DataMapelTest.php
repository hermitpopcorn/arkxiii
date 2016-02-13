<?php

use Illuminate\Database\Seeder;

class DataMapelTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        App\Mapel::create([
            'nama' => 'Wali kelas',
            'singkat' => 'WALI',
            'kelompok' => 'WK'
        ]);

        App\Mapel::create([
            'nama' => 'Matematika',
            'singkat' => 'MTK',
            'kelompok' => 'A',
        ]);

        App\Mapel::create([
            'nama' => 'Pendidikan Agama',
            'singkat' => 'Agama',
            'kelompok' => 'A',
        ]);
    }
}
