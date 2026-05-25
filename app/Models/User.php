<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'sector', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
/**
 * @mixin HasRoles
 *
 * @method bool hasRole(string|array $roles)
 * @method bool hasAnyRole(string|array $roles)
 * @method \Spatie\Permission\Models\Role|\Illuminate\Contracts\Support\Enumerable assignRole(...$roles)
 */
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function teachingClassrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'classroom_teacher')
            ->withTimestamps();
    }

    public function requestedAuthorizations(): HasMany
    {
        return $this->hasMany(Authorization::class, 'requested_by');
    }

    public function processedAuthorizations(): HasMany
    {
        return $this->hasMany(Authorization::class, 'processed_by');
    }

    public function canAccessClassroom(Classroom $classroom): bool
    {
        if ($this->hasRole(['admin', 'aqv', 'portaria'])) {
            return true;
        }

        if (! $this->hasRole('professor')) {
            return false;
        }

        if ($classroom->teacher_id === $this->id) {
            return true;
        }

        return $this->teachingClassrooms()
            ->whereKey($classroom->getKey())
            ->exists();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->hasRole(['admin', 'aqv', 'professor', 'portaria']);
    }
}
