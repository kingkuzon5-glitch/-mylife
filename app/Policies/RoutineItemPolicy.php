<?php

namespace App\Policies;

use App\Models\RoutineItem;
use App\Models\User;

class RoutineItemPolicy
{
    public function view(User $user, RoutineItem $routineItem): bool
    {
        return $user->id === $routineItem->user_id;
    }

    public function update(User $user, RoutineItem $routineItem): bool
    {
        return $user->id === $routineItem->user_id;
    }

    public function delete(User $user, RoutineItem $routineItem): bool
    {
        return $user->id === $routineItem->user_id;
    }
}
