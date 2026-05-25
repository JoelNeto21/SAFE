<?php

namespace App\Filament\Resources\Authorizations\Pages;

use App\Filament\Resources\Authorizations\AuthorizationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuthorization extends CreateRecord
{
    protected static string $resource = AuthorizationResource::class;

    protected static ?string $title = 'Nova autorização';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['requested_by'] = auth()->id();
        $data['event_at'] = now()->setTimeFromTimeString($data['event_time'] ?? now()->format('H:i'));

        unset($data['event_date'], $data['event_time']);

        return $data;
    }

    public function getBreadcrumb(): string
    {
        return 'Cadastrar';
    }
}
