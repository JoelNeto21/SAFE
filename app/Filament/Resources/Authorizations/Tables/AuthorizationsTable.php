<?php

namespace App\Filament\Resources\Authorizations\Tables;

use App\Models\User;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use App\Enums\AuthorizationStatus;
use Filament\Tables\Filters\SelectFilter;

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

                TextColumn::make('type.name')
                    ->label('Tipo'),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->label())
                    ->color(fn($state) => $state->color()),

                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Criado em'),

                TextColumn::make('student.classroom.name')
                    ->label('Turma'),

                TextColumn::make('processor.name')
                    ->label('Processado por')
                    ->placeholder('-')
                    ->visible(fn () => ! (self::currentUser()?->hasRole('portaria') ?? false)),

                TextColumn::make('authorized_at')
                    ->label('Autorizado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->visible(fn () => ! (self::currentUser()?->hasRole('portaria') ?? false)),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pendentes',
                        'approved' => 'Aprovados',
                        'denied' => 'Negados',
                        'finished' => 'Finalizados',
                    ]),

                SelectFilter::make('authorization_type_id')
                    ->relationship('type', 'name')
                    ->label('Tipo'),

                SelectFilter::make('student.classroom_id')
                    ->relationship('student.classroom', 'name')
                    ->label('Turma'),
            ])

            ->recordActions([
                Action::make('approve')
                    ->label('Aprovar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(
                        fn($record) =>
                        ($record->status === AuthorizationStatus::Pending)
                        && (self::currentUser()?->hasRole(['admin', 'aqv']) ?? false)
                    )
                    ->action(
                        fn($record) =>
                        $record->approve(Auth::user())
                    ),

                Action::make('deny')
                    ->label('Negar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(
                        fn($record) =>
                        ($record->status === AuthorizationStatus::Pending)
                        && (self::currentUser()?->hasRole(['admin', 'aqv']) ?? false)
                    )
                    ->action(
                        fn($record) =>
                        $record->deny(Auth::user())
                    ),

                Action::make('finish')
                    ->label('Finalizar')
                    ->icon('heroicon-o-flag')
                    ->color('gray')
                    ->visible(
                        fn($record) =>
                        ($record->status === AuthorizationStatus::Approved)
                        && (self::currentUser()?->hasRole(['admin', 'aqv']) ?? false)
                    )
                    ->action(
                        fn($record) =>
                        $record->finish()
                    ),
            ])

            ->emptyStateHeading('Nenhuma autorização encontrada')

            ->emptyStateDescription('Cadastre autorizações para começar.')

            ->emptyStateIcon('heroicon-o-arrow-right-circle');
    }
}
