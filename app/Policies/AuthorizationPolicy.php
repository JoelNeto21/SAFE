<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Authorization;

class AuthorizationPolicy
{
    /**
     * Ver lista
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole([
            'admin',
            'aqv',
            'professor',
            'portaria',
        ]);
    }

    /**
     * Ver item específico
     */
    public function view(User $user, Authorization $authorization): bool
    {
        if ($user->hasRole('admin') || $user->hasRole('aqv') || $user->hasRole('portaria')) {
            return true;
        }

        if ($user->hasRole('professor')) {
            return $authorization->student && $authorization->student->classroom && ($authorization->student->classroom->teacher_id === $user->id);
        }

        return false;
    }

    /**
     * Criar autorização
     */
    public function create(User $user): bool
    {
        return $user->hasRole([
            'admin',
            'aqv',
            'professor',
        ]);
    }

    /**
     * Editar autorização
     */
    public function update(User $user, Authorization $authorization): bool
    {
        return $user->hasRole([
            'admin',
            'aqv',
        ]);
    }

    /**
     * Deletar autorização
     */
    public function delete(User $user, Authorization $authorization): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Restaurar
     */
    public function restore(User $user, Authorization $authorization): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Force delete
     */
    public function forceDelete(User $user, Authorization $authorization): bool
    {
        return $user->hasRole('admin');
    }
}
