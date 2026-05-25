<?php

namespace App\Policies;

use App\Models\AuthorizationAudit;
use App\Models\User;

class AuthorizationAuditPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'aqv', 'professor', 'portaria']);
    }

    public function view(User $user, AuthorizationAudit $audit): bool
    {
        return $audit->authorization ? $user->can('view', $audit->authorization) : false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AuthorizationAudit $audit): bool
    {
        return false;
    }

    public function delete(User $user, AuthorizationAudit $audit): bool
    {
        return $user->hasRole('admin');
    }
}
