<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\UserGroup;
use Illuminate\Database\Seeder;

class UserGroupSeeder extends Seeder
{
    public function run(): void
    {
        $administrador = UserGroup::create([
            'descricao' => 'Administrador',
        ]);

        $administrador->menus()->sync(Menu::pluck('id'));
    }
}
