<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do funcionário')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('role_name')
                            ->label('Setor')
                            ->options([
                                'aqv' => 'AQV',
                                'portaria' => 'Portaria',
                                'professor' => 'Professor',
                                'admin' => 'Administrador',
                            ])
                            ->default('professor')
                            ->required()
                            ->live()
                            ->afterStateHydrated(function (Select $component, ?User $record): void {
                                $component->state($record?->roles()->first()?->name ?? $record?->sector ?? 'professor');
                            }),

                        TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->maxLength(255),

                        Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),

                        Select::make('teachingClassrooms')
                            ->label('Turmas vinculadas ao professor')
                            ->relationship('teachingClassrooms', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('role_name') === 'professor')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
