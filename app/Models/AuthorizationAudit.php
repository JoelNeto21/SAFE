<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorizationAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'authorization_id',
        'user_id',
        'action',
        'note',
    ];

    public function authorization()
    {
        return $this->belongsTo(Authorization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
