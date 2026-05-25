<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('authorizations', function (Blueprint $table) {
            $table->timestamp('event_at')->nullable()->after('status');
            $table->string('responsible_name')->nullable()->after('reason');
            $table->text('observations')->nullable()->after('responsible_name');
            $table->text('teacher_notes')->nullable()->after('observations');
            $table->text('gate_notes')->nullable()->after('teacher_notes');
            $table->timestamp('read_at')->nullable()->after('authorized_at');
            $table->timestamp('approved_at')->nullable()->after('read_at');
            $table->timestamp('completed_at')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('authorizations', function (Blueprint $table) {
            $table->dropColumn([
                'event_at',
                'responsible_name',
                'observations',
                'teacher_notes',
                'gate_notes',
                'read_at',
                'approved_at',
                'completed_at',
            ]);
        });
    }
};
