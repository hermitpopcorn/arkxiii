<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Model::unguard();

        $this->call(PengaturanDefault::class);
        $this->call(UserDefault::class);
        $this->call(WaliKelasEntry::class);

        /*
        $this->call(DataSemesterTest::class);

        $this->call(DataJurusanTest::class);
        $this->call(DataKelasTest::class);
        $this->call(DataSiswaTest::class);

        $this->call(DataGuruTest::class);
        $this->call(DataMapelTest::class);
        $this->call(DataKBTest::class);
        $this->call(DataMengajarTest::class);
        */

        Model::reguard();
    }
}
