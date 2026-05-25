<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do aluno')
                    ->description('Mantenha a matrícula e a turma sempre atualizadas para agilizar as autorizações.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->placeholder('Nome completo')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('registration')
                            ->label('Matrícula')
                            ->placeholder('Ex.: 2026001')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('classroom_id')
                            ->label('Turma')
                            ->relationship('classroom', 'name', modifyQueryUsing: function (Builder $query): Builder {
                                $user = auth()->user();

                                if ($user?->hasRole('professor')) {
                                    $query->where('teacher_id', $user->id)
                                        ->orWhereHas('teachers', fn (Builder $teachers) => $teachers->whereKey($user->id));
                                }

                                return $query->orderBy('course')->orderBy('name');
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
