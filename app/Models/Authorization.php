<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\AuthorizationStatus;
use App\Models\User;

class Authorization extends Model
{
    protected $fillable = [
        'student_id',
        'authorization_type_id',
        'requested_by',
        'processed_by',
        'status',
        'reason',
        'authorized_at',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function type()
    {
        return $this->belongsTo(AuthorizationType::class, 'authorization_type_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    protected $casts = [
        'authorized_at' => 'datetime',
        'status' => AuthorizationStatus::class,
    ];

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approve(User $user): void
    {
        $this->update([
            'status' => AuthorizationStatus::Approved,
            'authorized_at' => now(),
            'processed_by' => $user->id,
        ]);

        \App\Models\AuthorizationAudit::create([
            'authorization_id' => $this->id,
            'user_id' => $user->id,
            'action' => 'approved',
            'note' => null,
        ]);
    }

    public function deny(User $user): void
    {
        $this->update([
            'status' => AuthorizationStatus::Denied,
            'processed_by' => $user->id,
        ]);

        \App\Models\AuthorizationAudit::create([
            'authorization_id' => $this->id,
            'user_id' => $user->id,
            'action' => 'denied',
            'note' => null,
        ]);
    }

    public function finish(): void
    {
        $this->update([
            'status' => AuthorizationStatus::Finished,
        ]);

        \App\Models\AuthorizationAudit::create([
            'authorization_id' => $this->id,
            'user_id' => $this->processed_by,
            'action' => 'finished',
            'note' => null,
        ]);
    }
}
