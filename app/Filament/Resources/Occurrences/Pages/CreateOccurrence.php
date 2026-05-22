<?php

namespace App\Filament\Resources\Occurrences\Pages;

use App\Filament\Resources\Occurrences\OccurrenceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOccurrence extends CreateRecord
{
    protected static string $resource = OccurrenceResource::class;

    protected static ?string $title = 'Cadastrar ocorrência';

    public function getBreadcrumb(): string
    {
        return 'Cadastrar';
    }
}
