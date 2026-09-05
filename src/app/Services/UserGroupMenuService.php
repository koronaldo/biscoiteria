<?php

namespace App\Services;

use App\Models\UserGroup;

class UserGroupMenuService
{
    public function syncMenus(UserGroup $group, array $menuIds): void
    {
        $group->menus()->sync($menuIds);
    }
}
