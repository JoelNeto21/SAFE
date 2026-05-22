<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                TextInput::make('registration')
                    ->label('Matrícula')
                    ->required()
                    ->unique(ignoreRecord: true),

                Select::make('classroom_id')
                    ->label('Turma')
                    ->relationship('classroom', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
