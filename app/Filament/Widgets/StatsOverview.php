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
        return [
            Stat::make(
                'Autorizações',
                Authorization::count()
            )
                ->description('Total registradas')
                ->descriptionIcon('heroicon-m-document-text'),

            Stat::make(
                'Pendentes',
                Authorization::where('status', 'pending')->count()
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
