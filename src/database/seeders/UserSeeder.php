<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Ronaldo Ferreira da Silva',
            'email' => 'koronaldo@gmail.com',
            'password' => Hash::make('12345678'),
        ]);

        $administrador = UserGroup::where('descricao', 'Administrador')->first();

        if ($administrador) {
            $user->groups()->syncWithoutDetaching([$administrador->id]);
        }
    }
}
