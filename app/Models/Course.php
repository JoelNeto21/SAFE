<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Course extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected static function booted(): void
    {
        static::saving(function (Course $course): void {
            if (blank($course->slug) && filled($course->name)) {
                $course->slug = Str::slug($course->name);
            }
        });
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
