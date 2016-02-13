<?php

use Illuminate\Database\Seeder;

class WaliKelasEntry extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Mapel::create([
            'nama' => 'Wali kelas',
            'singkat' => 'WALI',
            'kelompok' => 'WK'
        ]);
    }
}
