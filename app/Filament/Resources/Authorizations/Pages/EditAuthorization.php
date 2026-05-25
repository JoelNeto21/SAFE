<?php

namespace App\Filament\Resources\Authorizations\Pages;

use App\Filament\Resources\Authorizations\AuthorizationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAuthorization extends EditRecord
{
    protected static string $resource = AuthorizationResource::class;

    protected static ?string $title = 'Editar autorização';

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['event_at'] = now()->setTimeFromTimeString($data['event_time'] ?? now()->format('H:i'));

        unset($data['event_date'], $data['event_time']);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function getBreadcrumb(): string
    {
        return 'Editar';
    }
}
