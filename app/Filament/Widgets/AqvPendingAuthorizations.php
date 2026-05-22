<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Authorization;

class AqvPendingAuthorizations extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        $query = Authorization::query();
        if ($user && $user->hasRole('professor')) {
            $query->whereHas('student.classroom', fn($q) => $q->where('teacher_id', $user->id));
        }

        return [
            Stat::make(
                'Pendentes',
                (clone $query)->where('status', 'pending')->count()
            ),

            Stat::make(
                'Aprovadas Hoje',
                (clone $query)->whereDate('updated_at', today())
                    ->where('status', 'approved')
                    ->count()
            ),
        ];
    }

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return $user?->hasRole([
            'admin',
            'aqv',
        ]) ?? false;
    }
}
