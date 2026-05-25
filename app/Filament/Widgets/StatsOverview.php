<?php

namespace App\Filament\Widgets;

use App\Models\Authorization;
use App\Models\Student;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class StatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected int|array|null $columns = 1;

    protected function getStats(): array
    {
        /** @var User|null $user */
        $user = auth()->user();

        $authorizationsQuery = Authorization::query();
        $studentsQuery = Student::query();
        if ($user && $user->hasRole('professor')) {
            $authorizationsQuery->where('teacher_id', $user->id);

            $studentsQuery->whereHas('classroom', function (Builder $query) use ($user): void {
                $query->where('teacher_id', $user->id)
                    ->orWhereHas('teachers', fn (Builder $teachers) => $teachers->whereKey($user->id));
            });
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
                $studentsQuery->count()
            )
                ->description('Total cadastrados')
                ->descriptionIcon('heroicon-m-academic-cap'),
        ];
    }
}
