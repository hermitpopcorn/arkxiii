<?php

use Illuminate\Database\Seeder;

class DataSemesterTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        App\Semester::create([
            'semester' => 1,
            'tahun_ajaran' => '2015 / 2016',
            'aktif' => 0,
        ]);

        App\Semester::create([
            'semester' => 2,
            'tahun_ajaran' => '2015 / 2016',
            'aktif' => 1,
        ]);
    }
}
