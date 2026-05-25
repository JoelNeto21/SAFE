<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'aqv', 'professor']);
    }

    public function view(User $user, Student $student): bool
    {
        return $student->classroom ? $user->canAccessClassroom($student->classroom) : false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'aqv']);
    }

    public function update(User $user, Student $student): bool
    {
        return $user->hasRole(['admin', 'aqv']);
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->hasRole('admin');
    }
}
