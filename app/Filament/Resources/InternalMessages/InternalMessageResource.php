<?php

namespace App\Filament\Resources\InternalMessages;

use App\Filament\Resources\InternalMessages\Pages\CreateInternalMessage;
use App\Filament\Resources\InternalMessages\Pages\EditInternalMessage;
use App\Filament\Resources\InternalMessages\Pages\ListInternalMessages;
use App\Filament\Resources\InternalMessages\Schemas\InternalMessageForm;
use App\Filament\Resources\InternalMessages\Tables\InternalMessagesTable;
use App\Models\InternalMessage;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class InternalMessageResource extends Resource
{
    protected static ?string $model = InternalMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Mensagens';

    protected static ?string $modelLabel = 'mensagem';

    protected static ?string $pluralModelLabel = 'mensagens';

    protected static string|UnitEnum|null $navigationGroup = 'Comunicação';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'subject';

    public static function form(Schema $schema): Schema
    {
        return InternalMessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InternalMessagesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var User|null $user */
        $user = auth()->user();
        if (! $user || $user->hasRole('admin')) {
            return $query;
        }

        $roles = $user->roles()->pluck('name')->all();

        return $query
            ->where('sender_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->orWhereIn('recipient_role', $roles);
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var User|null $user */
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        $roles = $user->roles()->pluck('name')->all();
        $count = InternalMessage::query()
            ->whereNull('read_at')
            ->where(function (Builder $query) use ($user, $roles): void {
                $query->where('recipient_id', $user->id)
                    ->orWhereIn('recipient_role', $roles);
            })
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInternalMessages::route('/'),
            'create' => CreateInternalMessage::route('/create'),
            'edit' => EditInternalMessage::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole(['admin', 'aqv', 'professor', 'portaria']) ?? false;
    }
}
