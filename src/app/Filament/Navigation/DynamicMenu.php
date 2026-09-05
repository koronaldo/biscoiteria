<?php

namespace App\Filament\Navigation;

use App\Models\Menu;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DynamicMenu
{
    public static function build(NavigationBuilder $builder): NavigationBuilder
    {
        $user = Auth::user();

        if (! $user) {
            return $builder;
        }

        $allowedIds = DB::table('menu_user_group')
            ->whereIn('user_group_id',
                DB::table('user_user_group')
                    ->where('user_id', $user->id)
                    ->pluck('user_group_id')
            )
            ->pluck('menu_id')
            ->unique()
            ->toArray();

        if (empty($allowedIds)) {
            return $builder;
        }

        $menus = Menu::with(['children' => function ($q) use ($allowedIds) {
            $q->where('active', true)
                ->whereIn('id', $allowedIds)
                ->orderBy('sort');
        }])
            ->whereNull('parent_id')
            ->where('active', true)
            ->orderBy('sort')
            ->get();

        foreach ($menus as $menu) {
            $items = [];

            foreach ($menu->children as $child) {
                $items[] = NavigationItem::make($child->title)
                    ->icon($child->icon)
                    ->url(route($child->route))
                    ->visible(true);
            }

            if (empty($items)) {
                continue;
            }

            $builder->group(
                NavigationGroup::make($menu->title)
                    ->items($items)
            );
        }

        return $builder;
    }
}
