<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

class ClassroomPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'aqv', 'professor']);
    }

    public function view(User $user, Classroom $classroom): bool
    {
        return $user->canAccessClassroom($classroom);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'aqv']);
    }

    public function update(User $user, Classroom $classroom): bool
    {
        return $user->hasRole(['admin', 'aqv']);
    }

    public function delete(User $user, Classroom $classroom): bool
    {
        return $user->hasRole('admin');
    }
}
