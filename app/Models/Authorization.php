<?php

namespace App\Models;

use App\Enums\AuthorizationStatus;
use App\Services\SafeNotifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Authorization extends Model
{
    protected $fillable = [
        'student_id',
        'authorization_type_id',
        'teacher_id',
        'requested_by',
        'processed_by',
        'status',
        'event_at',
        'missed_classes',
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
        'missed_classes' => 'array',
        'status' => AuthorizationStatus::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (Authorization $authorization): void {
            $authorization->normalizeAndValidateInstitutionalTime();
        });

        static::created(function (Authorization $authorization): void {
            $authorization->recordAudit(
                User::find($authorization->requested_by),
                'created',
                'Autorização criada no SAFE.',
            );

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

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
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

        $this->recordAudit($user, 'read', $note);

        app(SafeNotifier::class)->notifyAuthorizationRead($this, $user);
    }

    public function approve(User $user, ?string $note = null): void
    {
        $isExitFlow = $this->isExitFlow();
        $approvedAt = now();

        $this->update([
            'status' => $isExitFlow ? AuthorizationStatus::Approved : AuthorizationStatus::Finished,
            'authorized_at' => $approvedAt,
            'approved_at' => $approvedAt,
            'completed_at' => $isExitFlow ? $this->completed_at : $approvedAt,
            'processed_by' => $user->id,
            'teacher_notes' => $note ?: $this->teacher_notes,
        ]);

        $this->recordAudit(
            $user,
            $isExitFlow ? 'approved' : 'finished',
            $isExitFlow ? $note : ($note ?: 'Entrada finalizada automaticamente após aprovação do professor.'),
        );

        app(SafeNotifier::class)->notifyAuthorizationApproved($this, $user);
    }

    public function deny(User $user, ?string $note = null): void
    {
        $this->update([
            'status' => AuthorizationStatus::Denied,
            'processed_by' => $user->id,
            'teacher_notes' => $note ?: $this->teacher_notes,
        ]);

        $this->recordAudit($user, 'denied', $note);

        app(SafeNotifier::class)->notifyAuthorizationDenied($this, $user);
    }

    public function finish(?User $user = null, ?string $note = null): void
    {
        $this->update([
            'status' => AuthorizationStatus::Finished,
            'completed_at' => now(),
            'gate_notes' => $note ?: $this->gate_notes,
        ]);

        $this->recordAudit($user ?? User::find($this->processed_by), 'finished', $note);

        app(SafeNotifier::class)->notifyAuthorizationFinished($this, $user);
    }

    public function approveAtGate(User $user, ?string $note = null): void
    {
        $approvedAt = now();

        $this->update([
            'status' => AuthorizationStatus::Finished,
            'authorized_at' => $approvedAt,
            'approved_at' => $approvedAt,
            'completed_at' => $approvedAt,
            'processed_by' => $user->id,
            'gate_notes' => $note ?: $this->gate_notes,
        ]);

        $this->recordAudit($user, 'finished', $note ?: 'Saida aprovada e finalizada pela portaria.');

        app(SafeNotifier::class)->notifyAuthorizationFinished($this, $user);
    }

    public function recordAudit(?User $user, string $action, ?string $note = null): AuthorizationAudit
    {
        return $this->audits()->updateOrCreate(
            ['authorization_id' => $this->id],
            [
                'user_id' => $user?->id,
                'action' => $action,
                'note' => $note,
            ],
        );
    }

    public function isExitFlow(): bool
    {
        $type = Str::ascii(Str::lower($this->type?->name ?? ''));

        return str_contains($type, 'saida');
    }

    protected function normalizeAndValidateInstitutionalTime(): void
    {
        $allowedClasses = ['class_1', 'class_2', 'class_3', 'class_4', 'class_5'];
        $this->missed_classes = collect($this->missed_classes ?? [])
            ->filter(fn (mixed $class): bool => in_array($class, $allowedClasses, true))
            ->unique()
            ->values()
            ->all();

        if (! $this->event_at) {
            return;
        }

        $eventAt = Carbon::parse($this->event_at);
        $today = today();

        $eventAt->setDate($today->year, $today->month, $today->day);

        $minutes = ($eventAt->hour * 60) + $eventAt->minute;
        $minimum = (7 * 60) + 30;
        $maximum = 23 * 60;

        if ($minutes < $minimum || $minutes > $maximum) {
            throw ValidationException::withMessages([
                'event_time' => 'O horário deve estar entre 07:30 e 23:00.',
            ]);
        }

        $this->event_at = $eventAt;
    }
}
