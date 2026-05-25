<?php

namespace App\Models;

use App\Enums\AuthorizationStatus;
use App\Services\SafeNotifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Authorization extends Model
{
    protected $fillable = [
        'student_id',
        'authorization_type_id',
        'requested_by',
        'processed_by',
        'status',
        'event_at',
        'reason',
        'responsible_name',
        'observations',
        'teacher_notes',
        'gate_notes',
        'authorized_at',
        'read_at',
        'approved_at',
        'completed_at',
    ];

    protected $casts = [
        'event_at' => 'datetime',
        'authorized_at' => 'datetime',
        'read_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'status' => AuthorizationStatus::class,
    ];

    protected static function booted(): void
    {
        static::created(function (Authorization $authorization): void {
            $authorization->audits()->create([
                'user_id' => $authorization->requested_by,
                'action' => 'created',
                'note' => 'Autorização criada no SAFE.',
            ]);

            app(SafeNotifier::class)->notifyAuthorizationCreated($authorization);
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AuthorizationType::class, 'authorization_type_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(AuthorizationAudit::class);
    }

    public function markAsRead(User $user, ?string $note = null): void
    {
        $this->update([
            'read_at' => $this->read_at ?? now(),
        ]);

        $this->audits()->create([
            'user_id' => $user->id,
            'action' => 'read',
            'note' => $note,
        ]);

        app(SafeNotifier::class)->notifyAuthorizationRead($this, $user);
    }

    public function approve(User $user, ?string $note = null): void
    {
        $this->update([
            'status' => AuthorizationStatus::Approved,
            'authorized_at' => now(),
            'approved_at' => now(),
            'processed_by' => $user->id,
            'teacher_notes' => $note ?: $this->teacher_notes,
        ]);

        $this->audits()->create([
            'user_id' => $user->id,
            'action' => 'approved',
            'note' => $note,
        ]);

        app(SafeNotifier::class)->notifyAuthorizationApproved($this, $user);
    }

    public function deny(User $user, ?string $note = null): void
    {
        $this->update([
            'status' => AuthorizationStatus::Denied,
            'processed_by' => $user->id,
            'teacher_notes' => $note ?: $this->teacher_notes,
        ]);

        $this->audits()->create([
            'user_id' => $user->id,
            'action' => 'denied',
            'note' => $note,
        ]);

        app(SafeNotifier::class)->notifyAuthorizationDenied($this, $user);
    }

    public function finish(?User $user = null, ?string $note = null): void
    {
        $this->update([
            'status' => AuthorizationStatus::Finished,
            'completed_at' => now(),
            'gate_notes' => $note ?: $this->gate_notes,
        ]);

        $this->audits()->create([
            'user_id' => $user?->id ?? $this->processed_by,
            'action' => 'finished',
            'note' => $note,
        ]);

        app(SafeNotifier::class)->notifyAuthorizationFinished($this, $user);
    }

    public function isExitFlow(): bool
    {
        $type = Str::ascii(Str::lower($this->type?->name ?? ''));

        return str_contains($type, 'saida');
    }
}
