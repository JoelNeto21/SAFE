<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, User $employee): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, User $employee): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, User $employee): bool
    {
        return $user->hasRole('admin') && $user->isNot($employee);
    }
}
