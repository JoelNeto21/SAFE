<?php

namespace App\Filament\Resources\Authorizations\Schemas;

use App\Models\Student;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AuthorizationForm
{
    /**
     * @return array<int, string>
     */
    protected static function teacherOptionsForStudent(mixed $studentId): array
    {
        if (blank($studentId)) {
            return [];
        }

        $student = Student::query()
            ->with(['classroom.teacher', 'classroom.teachers' => fn ($query) => $query->role('professor')->orderBy('name')])
            ->find($studentId);

        $classroom = $student?->classroom;
        if (! $classroom) {
            return [];
        }

        $teachers = $classroom->teachers;
        if ($classroom->teacher?->hasRole('professor')) {
            $teachers->push($classroom->teacher);
        }

        return $teachers
            ->unique('id')
            ->sortBy('name')
            ->pluck('name', 'id')
            ->all();
    }

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
                            ->live()
                            ->afterStateUpdated(function (Set $set, mixed $state): void {
                                $options = self::teacherOptionsForStudent($state);

                                $set('teacher_id', count($options) === 1 ? array_key_first($options) : null);
                            })
                            ->required(),

                        Select::make('teacher_id')
                            ->label('Professor responsável')
                            ->options(fn (Get $get): array => self::teacherOptionsForStudent($get('student_id')))
                            ->searchable()
                            ->preload()
                            ->disabled(fn (Get $get): bool => blank($get('student_id')))
                            ->required(),

                        Select::make('authorization_type_id')
                            ->label('Tipo de autorização')
                            ->relationship('type', 'name')
                            ->required(),

                        DatePicker::make('event_date')
                            ->label('Data')
                            ->default(today())
                            ->disabled()
                            ->dehydrated(false),

                        TimePicker::make('event_time')
                            ->label('Horário')
                            ->seconds(false)
                            ->minDate('07:30')
                            ->maxDate('23:00')
                            ->minutesStep(1)
                            ->default(now()->format('H:i'))
                            ->format('H:i')
                            ->helperText('Permitido entre 07:30 e 23:00.')
                            ->afterStateHydrated(fn (TimePicker $component, $record) => $component->state($record?->event_at?->format('H:i') ?? now()->format('H:i')))
                            ->required(),

                        CheckboxList::make('missed_classes')
                            ->label('Aulas/períodos perdidos')
                            ->helperText('Opcional. Marque apenas quando houver perda de aulas no dia.')
                            ->options([
                                'class_1' => '1ª aula/período',
                                'class_2' => '2ª aula/período',
                                'class_3' => '3ª aula/período',
                                'class_4' => '4ª aula/período',
                                'class_5' => '5ª aula/período',
                            ])
                            ->columns(5)
                            ->columnSpanFull(),

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
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
