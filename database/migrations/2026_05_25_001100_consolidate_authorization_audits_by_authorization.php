<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $latestAuditIds = DB::table('authorization_audits')
            ->selectRaw('MAX(id) as id')
            ->groupBy('authorization_id')
            ->pluck('id')
            ->all();

        if ($latestAuditIds !== []) {
            DB::table('authorization_audits')
                ->whereNotIn('id', $latestAuditIds)
                ->delete();
        }

        Schema::table('authorization_audits', function ($table): void {
            $table->unique('authorization_id', 'authorization_audits_authorization_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('authorization_audits', function ($table): void {
            $table->dropUnique('authorization_audits_authorization_id_unique');
        });
    }
};
