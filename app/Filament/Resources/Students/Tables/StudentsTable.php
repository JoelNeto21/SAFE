<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Aluno')
                    ->searchable(),

                TextColumn::make('registration')
                    ->label('Matrícula')
                    ->searchable(),

                TextColumn::make('classroom.name')
                    ->label('Turma'),

                TextColumn::make('classroom.course')
                    ->label('Curso')
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Nenhum aluno encontrado')

            ->emptyStateDescription('Cadastre alunos para começar.')

            ->emptyStateIcon('heroicon-o-users')

            ->searchable();
    }
}
