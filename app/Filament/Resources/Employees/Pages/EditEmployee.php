<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected static ?string $title = 'Editar funcionário';

    protected string $roleName = 'professor';

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->roleName = $data['role_name'];
        $data['sector'] = $this->roleName;

        unset($data['role_name']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->syncRoles([$this->roleName]);

        if ($this->roleName !== 'professor') {
            $this->record->teachingClassrooms()->sync([]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
