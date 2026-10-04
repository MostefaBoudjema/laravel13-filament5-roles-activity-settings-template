<?php

namespace App\Policies;

use App\Models\AcademicYear;
use App\Models\User;

class AcademicYearPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_academic_year');
    }

    public function view(User $user, AcademicYear $academicYear): bool
    {
        return $user->can('view_academic_year');
    }

    public function create(User $user): bool
    {
        return $user->can('create_academic_year');
    }

    public function update(User $user, AcademicYear $academicYear): bool
    {
        return $user->can('update_academic_year');
    }

    public function delete(User $user, AcademicYear $academicYear): bool
    {
        return $user->can('delete_academic_year');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_academic_year');
    }

    public function restore(User $user, AcademicYear $academicYear): bool
    {
        return $user->can('delete_academic_year');
    }

    public function forceDelete(User $user, AcademicYear $academicYear): bool
    {
        return $user->can('delete_academic_year');
    }
}
