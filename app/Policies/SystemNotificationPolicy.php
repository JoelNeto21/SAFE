<?php

namespace App\Policies;

use App\Models\SystemNotification;
use App\Models\User;

class SystemNotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'aqv', 'professor', 'portaria']);
    }

    public function view(User $user, SystemNotification $notification): bool
    {
        return $notification->notifiable_type === User::class
            && (int) $notification->notifiable_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, SystemNotification $notification): bool
    {
        return $this->view($user, $notification);
    }

    public function delete(User $user, SystemNotification $notification): bool
    {
        return $this->view($user, $notification);
    }
}
