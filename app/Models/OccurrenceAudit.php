<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OccurrenceAudit extends Model
{
    protected $fillable = [
        'occurrence_id',
        'user_id',
        'action',
        'note',
    ];

    public function occurrence(): BelongsTo
    {
        return $this->belongsTo(Occurrence::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
