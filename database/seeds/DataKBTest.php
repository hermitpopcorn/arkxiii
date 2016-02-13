<?php

use Illuminate\Database\Seeder;

class DataKBTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('ketuntasan_belajar')->insert([
            'id_mapel' => 2,
            'id_semester' => 2,
            'kb_tingkat_1p' => 60,
            'kb_tingkat_1k' => 70,
            'kb_tingkat_2p' => 60,
            'kb_tingkat_2k' => 70,
            'kb_tingkat_3p' => 60,
            'kb_tingkat_3k' => 70
        ]);
    }
}
