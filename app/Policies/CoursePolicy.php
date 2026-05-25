<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'aqv', 'professor']);
    }

    public function view(User $user, Course $course): bool
    {
        return $user->hasRole(['admin', 'aqv'])
            || $user->teachingClassrooms()->where('course_id', $course->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'aqv']);
    }

    public function update(User $user, Course $course): bool
    {
        return $user->hasRole(['admin', 'aqv']);
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->hasRole('admin');
    }
}
