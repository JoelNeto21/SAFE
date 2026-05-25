<?php

namespace App\Filament\Resources\Notifications\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NotificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('read_at')
                    ->label('Lida')
                    ->boolean()
                    ->state(fn ($record) => filled($record->read_at)),

                TextColumn::make('title')
                    ->label('Título')
                    ->state(fn ($record) => data_get($record->data, 'title'))
                    ->searchable(query: fn ($query, string $search) => $query->where('data', 'like', "%{$search}%")),

                TextColumn::make('body')
                    ->label('Mensagem')
                    ->state(fn ($record) => data_get($record->data, 'body'))
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('category')
                    ->label('Categoria')
                    ->state(fn ($record) => data_get($record->data, 'category', 'info'))
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Criada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('read_at')
                    ->label('Status de leitura')
                    ->nullable()
                    ->trueLabel('Lidas')
                    ->falseLabel('Não lidas')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('read_at'),
                        false: fn ($query) => $query->whereNull('read_at'),
                        blank: fn ($query) => $query,
                    ),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Abrir')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record) => data_get($record->data, 'url'))
                    ->openUrlInNewTab(false)
                    ->visible(fn ($record) => filled(data_get($record->data, 'url'))),

                Action::make('markRead')
                    ->label('Marcar como lida')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn ($record) => blank($record->read_at))
                    ->action(fn ($record) => $record->markAsRead()),

                Action::make('markUnread')
                    ->label('Marcar como não lida')
                    ->icon('heroicon-o-envelope')
                    ->visible(fn ($record) => filled($record->read_at))
                    ->action(fn ($record) => $record->markAsUnread()),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Nenhuma notificação encontrada')
            ->emptyStateDescription('Alertas e atualizações importantes aparecerão aqui.')
            ->emptyStateIcon('heroicon-o-bell');
    }
}
