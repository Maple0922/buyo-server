<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\ReservationSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ReservationSeeder::class,
        ]);
    }
}
