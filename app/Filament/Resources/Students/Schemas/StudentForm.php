<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

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
                            ->placeholder('Ex.: DS2026101')
                            ->mask(RawJs::make(<<<'JS'
                                $input.toUpperCase().startsWith('ELT') ? 'aaa9999999' : 'aa9999999'
                            JS))
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Str::upper($state) : null)
                            ->regex('/^[A-Za-z]{2,3}\d{7}$/')
                            ->validationMessages([
                                'regex' => 'Use o formato DS2026101 ou ELT2026101.',
                            ])
                            ->required()
                            ->maxLength(10)
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
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
