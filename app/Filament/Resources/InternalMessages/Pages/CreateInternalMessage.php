<?php

namespace App\Filament\Resources\InternalMessages\Pages;

use App\Filament\Resources\InternalMessages\InternalMessageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInternalMessage extends CreateRecord
{
    protected static string $resource = InternalMessageResource::class;

    protected static ?string $title = 'Nova mensagem';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sender_id'] = auth()->id();

        return $data;
    }
}
