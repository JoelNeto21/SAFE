<?php

namespace App\Filament\Resources\Authorizations\Tables;

use App\Enums\AuthorizationStatus;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AuthorizationsTable
{
    protected static function currentUser(): ?User
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $user;
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label('Aluno')
                    ->searchable(),

                TextColumn::make('student.classroom.name')
                    ->label('Turma'),

                TextColumn::make('type.name')
                    ->label('Tipo'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => $state->color()),

                TextColumn::make('event_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Horário')
                    ->sortable(),

                TextColumn::make('responsible_name')
                    ->label('Responsável')
                    ->toggleable(),

                TextColumn::make('read_at')
                    ->label('Lida em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('processor.name')
                    ->label('Processado por')
                    ->placeholder('-')
                    ->visible(fn () => ! (self::currentUser()?->hasRole('portaria') ?? false)),

                TextColumn::make('authorized_at')
                    ->label('Autorizado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->visible(fn () => ! (self::currentUser()?->hasRole('portaria') ?? false)),

                TextColumn::make('completed_at')
                    ->label('Finalizado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendentes',
                        'approved' => 'Aprovadas',
                        'denied' => 'Recusadas',
                        'finished' => 'Finalizadas',
                    ]),

                SelectFilter::make('authorization_type_id')
                    ->relationship('type', 'name')
                    ->label('Tipo'),

                SelectFilter::make('student.classroom_id')
                    ->relationship('student.classroom', 'name')
                    ->label('Turma'),
            ])
            ->recordActions([
                Action::make('markRead')
                    ->label('Confirmar leitura')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn ($record) => blank($record->read_at) && (self::currentUser()?->hasRole(['admin', 'professor']) ?? false))
                    ->schema([
                        Textarea::make('note')
                            ->label('Observação')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->markAsRead(Auth::user(), $data['note'] ?? null);

                        Notification::make()
                            ->title('Leitura confirmada')
                            ->success()
                            ->send();
                    }),

                Action::make('approve')
                    ->label('Aprovar/liberar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === AuthorizationStatus::Pending
                        && (self::currentUser()?->hasRole(['admin', 'aqv', 'professor']) ?? false))
                    ->schema([
                        Textarea::make('note')
                            ->label('Observação do professor')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->approve(Auth::user(), $data['note'] ?? null);

                        Notification::make()
                            ->title('Autorização aprovada')
                            ->success()
                            ->send();
                    }),

                Action::make('deny')
                    ->label('Recusar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === AuthorizationStatus::Pending
                        && (self::currentUser()?->hasRole(['admin', 'aqv', 'professor']) ?? false))
                    ->schema([
                        Textarea::make('note')
                            ->label('Motivo da recusa')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->deny(Auth::user(), $data['note'] ?? null);

                        Notification::make()
                            ->title('Autorização recusada')
                            ->danger()
                            ->send();
                    }),

                Action::make('finish')
                    ->label('Confirmar saída/encerrar')
                    ->icon('heroicon-o-flag')
                    ->color('gray')
                    ->visible(fn ($record) => $record->status === AuthorizationStatus::Approved
                        && (self::currentUser()?->hasRole(['admin', 'aqv', 'portaria']) ?? false))
                    ->schema([
                        Textarea::make('note')
                            ->label('Observação final')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->finish(Auth::user(), $data['note'] ?? null);

                        Notification::make()
                            ->title('Fluxo finalizado')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
            ])
            ->emptyStateHeading('Nenhuma autorização encontrada')
            ->emptyStateDescription('Cadastre autorizações para começar.')
            ->emptyStateIcon('heroicon-o-arrow-right-circle');
    }
}
