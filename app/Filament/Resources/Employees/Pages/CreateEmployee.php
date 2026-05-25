<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected static ?string $title = 'Novo funcionário';

    protected string $roleName = 'professor';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->roleName = $data['role_name'];
        $data['sector'] = $this->roleName;

        unset($data['role_name']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncRoles([$this->roleName]);

        if ($this->roleName !== 'professor') {
            $this->record->teachingClassrooms()->sync([]);
        }
    }
}
