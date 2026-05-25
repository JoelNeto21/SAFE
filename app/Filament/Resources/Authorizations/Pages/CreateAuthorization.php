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

        return $data;
    }

    public function getBreadcrumb(): string
    {
        return 'Cadastrar';
    }
}
