<?php

namespace App\Policies;

use App\Models\OccurrenceAudit;
use App\Models\User;

class OccurrenceAuditPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'aqv', 'professor', 'portaria']);
    }

    public function view(User $user, OccurrenceAudit $audit): bool
    {
        return $audit->occurrence ? $user->can('view', $audit->occurrence) : false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, OccurrenceAudit $audit): bool
    {
        return false;
    }

    public function delete(User $user, OccurrenceAudit $audit): bool
    {
        return $user->hasRole('admin');
    }
}
