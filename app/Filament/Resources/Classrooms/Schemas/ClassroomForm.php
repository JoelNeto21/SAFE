<?php

namespace App\Filament\Resources\Classrooms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Support\Str;

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
                            ->mask(RawJs::make(<<<'JS'
                                $input.toUpperCase().startsWith('ELT') ? 'aaa-9a' : 'aa-9a'
                            JS))
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Str::upper($state) : null)
                            ->regex('/^[A-Za-z]{2,3}-\d[A-Za-z]$/')
                            ->validationMessages([
                                'regex' => 'Use o formato DS-1A ou ELT-1A.',
                            ])
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
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
