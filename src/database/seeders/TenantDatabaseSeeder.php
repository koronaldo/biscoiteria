<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LocalidadesSeeder::class,
            MenuSeeder::class,
            UserGroupSeeder::class,
            UserSeeder::class,
        ]);
    }
}
