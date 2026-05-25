<?php

namespace App\Policies;

use App\Models\InternalMessage;
use App\Models\User;

class InternalMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'aqv', 'professor', 'portaria']);
    }

    public function view(User $user, InternalMessage $message): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $message->sender_id === $user->id
            || $message->recipient_id === $user->id
            || ($message->recipient_role && $user->hasRole($message->recipient_role));
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, InternalMessage $message): bool
    {
        return $message->sender_id === $user->id || $user->hasRole('admin');
    }

    public function delete(User $user, InternalMessage $message): bool
    {
        return $message->sender_id === $user->id || $user->hasRole('admin');
    }
}
