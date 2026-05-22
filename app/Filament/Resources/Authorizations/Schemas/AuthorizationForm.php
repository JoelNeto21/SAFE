<?php

namespace App\Filament\Resources\Authorizations\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DateTimePicker;

class AuthorizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações da Autorização')
                    ->schema([

                        Select::make('student_id')
                            ->label('Aluno')
                            ->relationship('student', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('authorization_type_id')
                            ->label('Tipo')
                            ->relationship('type', 'name')
                            ->required(),

                        Textarea::make('reason')
                            ->label('Motivo')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),


                    ])
                    ->columns(2),

            ]);
    }
}
