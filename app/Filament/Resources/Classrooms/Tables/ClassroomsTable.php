<?php

namespace App\Filament\Resources\Classrooms\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassroomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Turma'),

                TextColumn::make('course')
                    ->label('Curso'),

                TextColumn::make('teachers.name')
                    ->label('Professores')
                    ->badge()
                    ->separator(', ')
                    ->toggleable(),

                TextColumn::make('students_count')
                    ->counts('students')
                    ->label('Alunos'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->emptyStateHeading('Nenhuma turma encontrada')

            ->emptyStateDescription('Cadastre turmas para começar.')

            ->emptyStateIcon('heroicon-o-academic-cap');
    }
}
