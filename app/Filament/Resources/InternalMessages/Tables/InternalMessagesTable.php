<?php

namespace App\Filament\Resources\InternalMessages\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InternalMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->label('Assunto')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sender.name')
                    ->label('Remetente')
                    ->placeholder('SAFE'),

                TextColumn::make('recipient.name')
                    ->label('Destinatário')
                    ->placeholder(fn ($record) => $record->recipient_role ? ucfirst($record->recipient_role) : '-'),

                IconColumn::make('read_at')
                    ->label('Lida')
                    ->boolean()
                    ->state(fn ($record) => filled($record->read_at)),

                TextColumn::make('created_at')
                    ->label('Enviada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('markRead')
                    ->label('Marcar como lida')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn ($record) => blank($record->read_at))
                    ->action(fn ($record) => $record->markAsRead()),
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Nenhuma mensagem encontrada')
            ->emptyStateDescription('Envie mensagens internas entre AQV, professores e portaria.')
            ->emptyStateIcon('heroicon-o-envelope');
    }
}
