<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Authorization;

class AuthorizationChart extends ChartWidget
{
    protected ?string $heading = 'Authorization Chart';

    protected function getData(): array
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        $query = Authorization::query();
        if ($user && $user->hasRole('professor')) {
            $query->whereHas('student.classroom', fn($q) => $q->where('teacher_id', $user->id));
        }

        return [
            'datasets' => [
                [
                    'label' => 'Autorizações',
                    'data' => [
                        (clone $query)->where('status', 'pending')->count(),
                        (clone $query)->where('status', 'approved')->count(),
                        (clone $query)->where('status', 'finished')->count(),
                    ],
                ],
            ],

            'labels' => [
                'Pendentes',
                'Aprovadas',
                'Finalizadas',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
