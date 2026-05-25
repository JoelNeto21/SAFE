<?php

namespace App\Filament\Resources\Classrooms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassroomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados da turma')
                    ->description('Organize os alunos por turma, curso e professor responsável.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Turma')
                            ->placeholder('Ex.: DS-1A')
                            ->required()
                            ->maxLength(255),

                        Select::make('course_id')
                            ->label('Curso')
                            ->relationship('course', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('teachers')
                            ->label('Professores com acesso')
                            ->relationship('teachers', 'name', modifyQueryUsing: fn ($query) => $query->role('professor')->orderBy('name'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
