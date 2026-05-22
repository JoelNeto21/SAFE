<?php

namespace App\Filament\Resources\Classrooms\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class ClassroomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Turma')
                    ->required(),

                TextInput::make('course')
                    ->label('Curso')
                    ->required(),
            ]);
    }
}
