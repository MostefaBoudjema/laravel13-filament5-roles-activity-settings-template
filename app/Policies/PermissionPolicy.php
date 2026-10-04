<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_permission');
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->can('view_permission');
    }

    public function create(User $user): bool
    {
        return $user->can('create_permission');
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->can('update_permission');
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->can('delete_permission');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_permission');
    }

    public function restore(User $user, Permission $permission): bool
    {
        return $user->can('delete_permission');
    }

    public function forceDelete(User $user, Permission $permission): bool
    {
        return $user->can('delete_permission');
    }
}
