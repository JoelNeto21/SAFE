<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    protected $fillable = [
        'name',
        'course_id',
        'course',
        'teacher_id',
    ];

    protected static function booted(): void
    {
        static::saving(function (Classroom $classroom): void {
            if ($classroom->course_id && ($classroom->isDirty('course_id') || blank($classroom->course))) {
                $classroom->course = Course::query()->whereKey($classroom->course_id)->value('name');
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'classroom_teacher')
            ->withTimestamps();
    }
}
