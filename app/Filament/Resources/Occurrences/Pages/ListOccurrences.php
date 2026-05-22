<?php

namespace App\Filament\Resources\Occurrences\Pages;

use App\Filament\Resources\Occurrences\OccurrenceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOccurrences extends ListRecords
{
    protected static string $resource = OccurrenceResource::class;

    protected static ?string $title = 'Ocorrências';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getBreadcrumb(): string
    {
        return 'Lista';
    }
}
