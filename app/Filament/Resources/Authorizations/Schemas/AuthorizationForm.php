<?php

namespace App\Filament\Resources\Authorizations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AuthorizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Autorização digital')
                    ->description('Registre aluno, turma, horário, motivo, responsável e observações do fluxo SAFE.')
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

                        Select::make('authorization_type_id')
                            ->label('Tipo de autorização')
                            ->relationship('type', 'name')
                            ->required(),

                        DateTimePicker::make('event_at')
                            ->label('Horário')
                            ->seconds(false)
                            ->default(now())
                            ->required(),

                        TextInput::make('responsible_name')
                            ->label('Responsável')
                            ->placeholder('Nome do responsável pelo pedido')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('reason')
                            ->label('Motivo')
                            ->placeholder('Descreva o motivo da entrada ou saída autorizada.')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('observations')
                            ->label('Observações da AQV/Portaria')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('teacher_notes')
                            ->label('Observações do professor')
                            ->rows(3)
                            ->disabled(fn () => ! (auth()->user()?->hasRole(['admin', 'aqv']) ?? false))
                            ->columnSpanFull(),

                        Textarea::make('gate_notes')
                            ->label('Observações da portaria')
                            ->rows(3)
                            ->disabled(fn () => ! (auth()->user()?->hasRole(['admin', 'aqv', 'portaria']) ?? false))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
