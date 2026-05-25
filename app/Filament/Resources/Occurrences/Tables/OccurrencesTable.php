<?php

namespace App\Filament\Resources\Occurrences\Tables;

use App\Enums\OccurrenceStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class OccurrencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label('Aluno')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student.classroom.name')
                    ->label('Turma'),

                TextColumn::make('registrar.name')
                    ->label('Registrado por')
                    ->placeholder('SAFE'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => $state->color()),

                TextColumn::make('occurred_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(OccurrenceStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()])),
            ])
            ->recordActions([
                Action::make('close')
                    ->label('Encerrar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== OccurrenceStatus::Closed && (auth()->user()?->hasRole(['admin', 'aqv']) ?? false))
                    ->schema([
                        Textarea::make('note')
                            ->label('Observação de encerramento')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->close(Auth::user(), $data['note'] ?? null);

                        Notification::make()
                            ->title('Ocorrência encerrada')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Nenhuma ocorrência encontrada')
            ->emptyStateDescription('Cadastre ocorrências para começar.')
            ->emptyStateIcon('heroicon-o-exclamation-triangle');
    }
}
