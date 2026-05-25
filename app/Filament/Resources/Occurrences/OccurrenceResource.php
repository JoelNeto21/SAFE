<?php

namespace App\Filament\Resources\Occurrences;

use App\Filament\Resources\Occurrences\Pages\CreateOccurrence;
use App\Filament\Resources\Occurrences\Pages\EditOccurrence;
use App\Filament\Resources\Occurrences\Pages\ListOccurrences;
use App\Filament\Resources\Occurrences\Schemas\OccurrenceForm;
use App\Filament\Resources\Occurrences\Tables\OccurrencesTable;
use App\Models\Occurrence;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class OccurrenceResource extends Resource
{
    protected static ?string $model = Occurrence::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $navigationLabel = 'Ocorrências';

    protected static ?string $modelLabel = 'ocorrência';

    protected static ?string $pluralModelLabel = 'ocorrências';

    protected static string|UnitEnum|null $navigationGroup = 'Operação escolar';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return OccurrenceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OccurrencesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('professor')) {
            return $query->whereHas('student.classroom', function (Builder $query) use ($user): void {
                $query
                    ->where('teacher_id', $user->id)
                    ->orWhereHas('teachers', fn (Builder $teachers) => $teachers->whereKey($user->id));
            });
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
            'index' => ListOccurrences::route('/'),
            'create' => CreateOccurrence::route('/create'),
            'edit' => EditOccurrence::route('/{record}/edit'),
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
