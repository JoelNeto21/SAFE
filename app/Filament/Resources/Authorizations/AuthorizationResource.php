<?php

namespace App\Filament\Resources\Authorizations;

use App\Enums\AuthorizationStatus;
use App\Filament\Resources\Authorizations\Pages\CreateAuthorization;
use App\Filament\Resources\Authorizations\Pages\EditAuthorization;
use App\Filament\Resources\Authorizations\Pages\ListAuthorizations;
use App\Filament\Resources\Authorizations\Schemas\AuthorizationForm;
use App\Filament\Resources\Authorizations\Tables\AuthorizationsTable;
use App\Models\Authorization;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AuthorizationResource extends Resource
{
    protected static ?string $model = Authorization::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowRightCircle;

    protected static ?string $navigationLabel = 'Autorizações';

    protected static ?string $modelLabel = 'autorização';

    protected static ?string $pluralModelLabel = 'autorizações';

    protected static string|UnitEnum|null $navigationGroup = 'Operação escolar';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return AuthorizationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuthorizationsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->where('status', '!=', AuthorizationStatus::Finished->value);

        /** @var User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('professor')) {
            return $query->where('teacher_id', $user->id);
        }

        if ($user && $user->hasRole('portaria')) {
            // Portaria should see only minimal fields; keep full query but UI will hide columns/actions.
            return $query;
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuthorizations::route('/'),
            'create' => CreateAuthorization::route('/create'),
            'edit' => EditAuthorization::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user?->hasRole([
            'admin',
            'aqv',
            'professor',
            'portaria',
        ]) ?? false;
    }
}
