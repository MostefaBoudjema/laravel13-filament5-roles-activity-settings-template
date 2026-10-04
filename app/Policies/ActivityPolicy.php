<?php

namespace App\Policies;

use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_activity_log');
    }

    public function view(User $user, Activity $activity): bool
    {
        return $user->can('view_activity_log');
    }

    public function create(User $user): bool
    {
        return $user->can('create_activity_log');
    }

    public function update(User $user, Activity $activity): bool
    {
        return $user->can('update_activity_log');
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $user->can('delete_activity_log');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_activity_log');
    }

    public function restore(User $user, Activity $activity): bool
    {
        return $user->can('delete_activity_log');
    }

    public function forceDelete(User $user, Activity $activity): bool
    {
        return $user->can('delete_activity_log');
    }
}
