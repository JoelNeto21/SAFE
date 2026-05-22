<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Authorization;

class AqvPendingAuthorizations extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Pendentes',
                Authorization::where('status', 'pending')->count()
            ),

            Stat::make(
                'Aprovadas Hoje',
                Authorization::whereDate('updated_at', today())
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
