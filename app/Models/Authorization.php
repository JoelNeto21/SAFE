<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\AuthorizationStatus;

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

    protected function casts(): array
    {
        return [
            'authorized_at' => 'datetime',
            'status' => AuthorizationStatus::class,
        ];
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approve(User $user): void
    {
        $this->update([
            'status' => AuthorizationStatus::Approved,
            'authorized_at' => now(),
            'requested_by' => $user->id,
            'processed_by' => $user->id,
        ]);
    }

    public function deny(User $user): void
    {
        $this->update([
            'status' => AuthorizationStatus::Denied,
            'requested_by' => $user->id,
            'processed_by' => $user->id,
        ]);
    }

    public function finish(): void
    {
        $this->update([
            'status' => 'finished',
        ]);
    }
}
