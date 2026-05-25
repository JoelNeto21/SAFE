<?php

namespace App\Filament\Widgets;

use App\Models\Authorization;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AqvPendingAuthorizations extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        /** @var User|null $user */
        $user = auth()->user();
        $query = Authorization::query();
        if ($user && $user->hasRole('professor')) {
            $query->where('teacher_id', $user->id);
        }

        return [
            Stat::make(
                'Pendentes',
                (clone $query)->where('status', 'pending')->count()
            ),

            Stat::make(
                'Aprovadas hoje',
                (clone $query)->whereDate('updated_at', today())
                    ->where('status', 'approved')
                    ->count()
            ),
        ];
    }

    public static function canView(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user?->hasRole([
            'admin',
            'aqv',
        ]) ?? false;
    }
}
