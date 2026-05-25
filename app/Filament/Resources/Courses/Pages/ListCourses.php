<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    protected static ?string $title = 'Cursos';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
