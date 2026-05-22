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
        return $user->hasRole([
            'admin',
            'aqv',
            'professor',
            'portaria',
        ]);
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
