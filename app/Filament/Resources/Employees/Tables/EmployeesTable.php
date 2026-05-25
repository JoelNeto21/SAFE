<?php

namespace App\Filament\Resources\Employees\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->label('Setor')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Administrador',
                        'aqv' => 'AQV',
                        'portaria' => 'Portaria',
                        'professor' => 'Professor',
                        default => $state,
                    }),

                TextColumn::make('teachingClassrooms.name')
                    ->label('Turmas')
                    ->badge()
                    ->separator(', ')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Setor')
                    ->relationship('roles', 'name')
                    ->options([
                        'admin' => 'Administrador',
                        'aqv' => 'AQV',
                        'professor' => 'Professor',
                        'portaria' => 'Portaria',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Nenhum funcionário encontrado')
            ->emptyStateDescription('Cadastre os setores que usarão o SAFE.')
            ->emptyStateIcon('heroicon-o-user-group');
    }
}
