<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorizationAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'authorization_id',
        'user_id',
        'action',
        'note',
    ];

    public function authorization(): BelongsTo
    {
        return $this->belongsTo(Authorization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
