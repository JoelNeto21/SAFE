<?php

namespace App\Models;

use App\Enums\OccurrenceStatus;
use App\Services\SafeNotifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Occurrence extends Model
{
    protected $fillable = [
        'student_id',
        'registered_by',
        'description',
        'occurred_at',
        'status',
        'observations',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'status' => OccurrenceStatus::class,
    ];

    protected static function booted(): void
    {
        static::created(function (Occurrence $occurrence): void {
            $occurrence->audits()->create([
                'user_id' => $occurrence->registered_by,
                'action' => 'created',
                'note' => 'Ocorrência registrada no SAFE.',
            ]);

            app(SafeNotifier::class)->notifyOccurrenceCreated($occurrence);
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function registrar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(OccurrenceAudit::class);
    }

    public function close(User $user, ?string $note = null): void
    {
        $this->update([
            'status' => OccurrenceStatus::Closed,
            'observations' => $note ?: $this->observations,
        ]);

        $this->audits()->create([
            'user_id' => $user->id,
            'action' => 'closed',
            'note' => $note,
        ]);
    }
}
