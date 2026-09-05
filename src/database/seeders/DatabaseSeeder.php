<?php

namespace Database\Seeders;

use Database\Seeders\Central\CentralDatabaseSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CentralDatabaseSeeder::class);
    }
}
