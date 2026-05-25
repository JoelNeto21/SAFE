<?php

namespace App\Filament\Widgets;

use App\Models\Authorization;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class AuthorizationChart extends ChartWidget
{
    protected ?string $heading = 'Autorizações por status';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 2,
    ];

    protected ?string $maxHeight = '22rem';

    protected function getData(): array
    {
        /** @var User|null $user */
        $user = auth()->user();
        $query = Authorization::query();
        if ($user && $user->hasRole('professor')) {
            $query->where('teacher_id', $user->id);
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
                    'backgroundColor' => [
                        '#f59e0b',
                        '#22c55e',
                        '#ef4444',
                        '#71717a',
                    ],
                    'borderColor' => '#ffffff',
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
        return 'pie';
    }
}
