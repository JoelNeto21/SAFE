<?php

namespace App\Filament\Widgets;

use App\Models\Authorization;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class AuthorizationChart extends ChartWidget
{
    protected ?string $heading = 'Autorizações por status';

    protected function getData(): array
    {
        /** @var User|null $user */
        $user = auth()->user();
        $query = Authorization::query();
        if ($user && $user->hasRole('professor')) {
            $query->whereHas('student.classroom', function (Builder $query) use ($user): void {
                $query->where('teacher_id', $user->id)
                    ->orWhereHas('teachers', fn (Builder $teachers) => $teachers->whereKey($user->id));
            });
        }

        return [
            'datasets' => [
                [
                    'label' => 'Autorizações',
                    'data' => [
                        (clone $query)->where('status', 'pending')->count(),
                        (clone $query)->where('status', 'approved')->count(),
                        (clone $query)->where('status', 'denied')->count(),
                        (clone $query)->where('status', 'finished')->count(),
                    ],
                ],
            ],

            'labels' => [
                'Pendentes',
                'Aprovadas',
                'Recusadas',
                'Finalizadas',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
