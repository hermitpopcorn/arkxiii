<?php

use Illuminate\Database\Seeder;

class DataMengajarTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        App\Mengajar::create([
            'id_guru' => 1,
            'id_kelas' => 1,
            'id_mapel' => 1,
            'id_semester' => 2
        ]);
    }
}
