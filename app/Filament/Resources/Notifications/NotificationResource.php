<?php

namespace App\Filament\Resources\Notifications;

use App\Filament\Resources\Notifications\Pages\ListNotifications;
use App\Filament\Resources\Notifications\Tables\NotificationsTable;
use App\Models\SystemNotification;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class NotificationResource extends Resource
{
    protected static ?string $model = SystemNotification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static ?string $navigationLabel = 'Notificações';

    protected static ?string $modelLabel = 'notificação';

    protected static ?string $pluralModelLabel = 'notificações';

    protected static string|UnitEnum|null $navigationGroup = 'Comunicação';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return NotificationsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', auth()->id());
    }

    public static function getNavigationBadge(): ?string
    {
        $count = SystemNotification::query()
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotifications::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole(['admin', 'aqv', 'professor', 'portaria']) ?? false;
    }
}
