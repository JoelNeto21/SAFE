<?php

namespace App\Filament\Resources\Occurrences\Schemas;

use App\Enums\OccurrenceStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class OccurrenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Registro da ocorrência')
                    ->description('Descreva situações relevantes para acompanhamento escolar.')
                    ->schema([
                        Select::make('student_id')
                            ->label('Aluno')
                            ->relationship('student', 'name', modifyQueryUsing: function (Builder $query): Builder {
                                $user = auth()->user();

                                if ($user?->hasRole('professor')) {
                                    $query->whereHas('classroom', function (Builder $classrooms) use ($user): void {
                                        $classrooms->where('teacher_id', $user->id)
                                            ->orWhereHas('teachers', fn (Builder $teachers) => $teachers->whereKey($user->id));
                                    });
                                }

                                return $query->orderBy('name');
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        DateTimePicker::make('occurred_at')
                            ->label('Data da ocorrência')
                            ->seconds(false)
                            ->default(now())
                            ->required(),

                        Select::make('status')
                            ->label('Status')
                            ->options(collect(OccurrenceStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()]))
                            ->default(OccurrenceStatus::Open->value)
                            ->required(),

                        Textarea::make('description')
                            ->label('Descrição')
                            ->placeholder('Descreva a ocorrência com objetividade.')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('observations')
                            ->label('Observações')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
