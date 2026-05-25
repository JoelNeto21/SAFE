<?php

use App\Models\Course;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->foreignId('course_id')
                ->nullable()
                ->after('id')
                ->constrained('courses')
                ->nullOnDelete();
        });

        DB::table('classrooms')
            ->select('course')
            ->whereNotNull('course')
            ->distinct()
            ->orderBy('course')
            ->get()
            ->each(function (object $classroom): void {
                $course = Course::firstOrCreate(
                    ['name' => $classroom->course],
                    ['slug' => Str::slug($classroom->course)]
                );

                DB::table('classrooms')
                    ->where('course', $classroom->course)
                    ->update(['course_id' => $course->id]);
            });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_id');
        });
    }
};
