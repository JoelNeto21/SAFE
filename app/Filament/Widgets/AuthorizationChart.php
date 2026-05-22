<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Authorization;

class AuthorizationChart extends ChartWidget
{
    protected ?string $heading = 'Authorization Chart';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Autorizações',
                    'data' => [
                        Authorization::where('status', 'pending')->count(),
                        Authorization::where('status', 'approved')->count(),
                        Authorization::where('status', 'finished')->count(),
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
