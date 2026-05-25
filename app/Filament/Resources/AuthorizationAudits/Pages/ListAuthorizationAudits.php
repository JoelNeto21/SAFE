<?php

namespace App\Filament\Resources\AuthorizationAudits\Pages;

use App\Filament\Resources\AuthorizationAudits\AuthorizationAuditResource;
use Filament\Resources\Pages\ListRecords;

class ListAuthorizationAudits extends ListRecords
{
    protected static string $resource = AuthorizationAuditResource::class;

    protected static ?string $title = 'Histórico de autorizações';
}
