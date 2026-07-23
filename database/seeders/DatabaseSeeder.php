<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            KecamatanSeeder::class,
            DesaSeeder::class,
            UserSeeder::class,
            DataIDMSeeder::class,
        ]);
    }
}