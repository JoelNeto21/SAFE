<?php

namespace App\Filament\Resources\Courses\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Curso')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('classrooms_count')
                    ->counts('classrooms')
                    ->label('Turmas'),

                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Nenhum curso encontrado')
            ->emptyStateDescription('Cadastre cursos para organizar as turmas.')
            ->emptyStateIcon('heroicon-o-book-open');
    }
}
