<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do curso')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state)))
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Identificador')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
