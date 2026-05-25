<?php

namespace App\Filament\Resources\OccurrenceAudits\Pages;

use App\Filament\Resources\OccurrenceAudits\OccurrenceAuditResource;
use Filament\Resources\Pages\ListRecords;

class ListOccurrenceAudits extends ListRecords
{
    protected static string $resource = OccurrenceAuditResource::class;

    protected static ?string $title = 'Histórico de ocorrências';
}
