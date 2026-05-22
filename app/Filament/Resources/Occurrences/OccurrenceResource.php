<?php

namespace App\Filament\Resources\Occurrences;

use App\Filament\Resources\Occurrences\Pages\CreateOccurrence;
use App\Filament\Resources\Occurrences\Pages\EditOccurrence;
use App\Filament\Resources\Occurrences\Pages\ListOccurrences;
use App\Filament\Resources\Occurrences\Schemas\OccurrenceForm;
use App\Filament\Resources\Occurrences\Tables\OccurrencesTable;
use App\Models\Occurrence;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OccurrenceResource extends Resource
{
    protected static ?string $model = Occurrence::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $navigationLabel = 'Ocorrências';

    protected static ?string $modelLabel = 'Ocorrência';

    protected static ?string $pluralModelLabel = 'Ocorrências';

    protected static string|UnitEnum|null $navigationGroup = 'Fluxo Escolar';

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
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return $user?->hasRole([
            'admin',
            'aqv',
        ]) ?? false;
    }
}
