<?php

use Illuminate\Database\Seeder;

class DataJurusanTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        App\Jurusan::create([
            'singkat' => 'RPL',
            'lengkap' => 'Rekayasa Perangkat Lunak',
        ]);

        App\Jurusan::create([
            'singkat' => 'KP',
            'lengkap' => 'Kontrol Proses',
        ]);
    }
}
