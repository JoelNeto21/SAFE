<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Authorization;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        $authorizationsQuery = Authorization::query();
        if ($user && $user->hasRole('professor')) {
            $authorizationsQuery->whereHas('student.classroom', fn($q) => $q->where('teacher_id', $user->id));
        }

        return [
            Stat::make(
                'Autorizações',
                $authorizationsQuery->count()
            )
                ->description('Total registradas')
                ->descriptionIcon('heroicon-m-document-text'),

            Stat::make(
                'Pendentes',
                (clone $authorizationsQuery)->where('status', 'pending')->count()
            )
                ->description('Aguardando aprovação')
                ->descriptionIcon('heroicon-m-clock'),

            Stat::make(
                'Alunos',
                Student::count()
            )
                ->description('Total cadastrados')
                ->descriptionIcon('heroicon-m-academic-cap'),
        ];
    }
}
