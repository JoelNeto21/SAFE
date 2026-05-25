<?php

namespace App\Policies;

use App\Models\Occurrence;
use App\Models\User;

class OccurrencePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'aqv', 'professor', 'portaria']);
    }

    public function view(User $user, Occurrence $occurrence): bool
    {
        if ($user->hasRole(['admin', 'aqv', 'portaria'])) {
            return true;
        }

        return $occurrence->student?->classroom
            ? $user->canAccessClassroom($occurrence->student->classroom)
            : false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'aqv', 'professor', 'portaria']);
    }

    public function update(User $user, Occurrence $occurrence): bool
    {
        return $user->hasRole(['admin', 'aqv']);
    }

    public function delete(User $user, Occurrence $occurrence): bool
    {
        return $user->hasRole('admin');
    }
}
