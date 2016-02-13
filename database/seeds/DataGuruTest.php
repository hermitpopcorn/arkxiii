<?php

use Illuminate\Database\Seeder;

class DataGuruTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker\Factory::create('id_ID');

        App\Guru::create([
            'nip' => '000',
            'nama' => $faker->name,
            'username' => '000',
            'password' => bcrypt('000'),
            'staf' => 1,
        ]);

        App\Guru::create([
            'nip' => '111',
            'nama' => $faker->name,
            'username' => '111',
            'password' => bcrypt('111'),
            'staf' => 2,
        ]);
        
        App\Guru::create([
            'nip' => '555',
            'nama' => $faker->name,
            'username' => '555',
            'password' => bcrypt('555'),
            'staf' => 0
        ]);
    }
}
