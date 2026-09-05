<?php

namespace App\Services;

use App\Models\User;

class UserGroupAssignmentService
{
    public function syncGroups(User $user, array $groupIds): void
    {
        $user->groups()->sync($groupIds);
    }
}
