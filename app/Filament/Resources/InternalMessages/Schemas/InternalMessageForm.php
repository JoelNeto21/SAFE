<?php

namespace App\Filament\Resources\InternalMessages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InternalMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mensagem interna')
                    ->schema([
                        Select::make('recipient_id')
                            ->label('Destinatário')
                            ->relationship('recipient', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('recipient_role')
                            ->label('Setor destinatário')
                            ->options([
                                'aqv' => 'AQV',
                                'portaria' => 'Portaria',
                                'professor' => 'Professores',
                                'admin' => 'Administração',
                            ]),

                        TextInput::make('subject')
                            ->label('Assunto')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('body')
                            ->label('Mensagem')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
