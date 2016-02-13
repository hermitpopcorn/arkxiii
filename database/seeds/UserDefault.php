<?php

use Illuminate\Database\Seeder;

class UserDefault extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        App\Guru::create([
            'nip' => '000',
            'nama' => 'DEFAULT',
            'username' => '000',
            'password' => bcrypt('000'),
            'staf' => 1
        ]);
    }
}
