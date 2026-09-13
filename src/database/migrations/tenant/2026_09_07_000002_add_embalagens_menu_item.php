<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $produtos = DB::table('menus')
            ->where('route', 'filament.app.resources.produtos.index')
            ->first();

        if (! $produtos) {
            return;
        }

        $embalagemId = DB::table('menus')->insertGetId([
            'title' => 'Embalagens',
            'icon' => 'heroicon-o-archive-box',
            'route' => 'filament.app.resources.embalagens.index',
            'parent_id' => $produtos->parent_id,
            'sort' => $produtos->sort + 1,
            'active' => true,
            'admin' => $produtos->admin,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $grupos = DB::table('menu_user_group')
            ->where('menu_id', $produtos->id)
            ->pluck('user_group_id');

        foreach ($grupos as $userGroupId) {
            DB::table('menu_user_group')->insert([
                'menu_id' => $embalagemId,
                'user_group_id' => $userGroupId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        $menu = DB::table('menus')
            ->where('route', 'filament.app.resources.embalagens.index')
            ->first();

        if ($menu) {
            DB::table('menu_user_group')->where('menu_id', $menu->id)->delete();
            DB::table('menus')->where('id', $menu->id)->delete();
        }
    }
};
