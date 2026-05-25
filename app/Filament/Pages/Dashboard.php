<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AuthorizationChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;

class Dashboard extends BaseDashboard
{
    /**
     * @return array<class-string<Widget>|WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            AuthorizationChart::class,
            StatsOverview::class,
        ];
    }

    /**
     * @return array<string, int>
     */
    public function getColumns(): array
    {
        return [
            'default' => 1,
            'xl' => 3,
        ];
    }

    /**
     * @return array<string>
     */
    public function getPageClasses(): array
    {
        return ['fi-dashboard-page'];
    }
}
