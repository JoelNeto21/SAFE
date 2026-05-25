<?php

namespace App\Filament\Resources\OccurrenceAudits;

use App\Filament\Resources\OccurrenceAudits\Pages\ListOccurrenceAudits;
use App\Models\OccurrenceAudit;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class OccurrenceAuditResource extends Resource
{
    protected static ?string $model = OccurrenceAudit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Histórico de ocorrências';

    protected static ?string $modelLabel = 'histórico de ocorrência';

    protected static ?string $pluralModelLabel = 'histórico de ocorrências';

    protected static string|UnitEnum|null $navigationGroup = 'Operação escolar';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('occurrence.id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('occurrence.student.name')
                    ->label('Aluno')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Usuário')
                    ->placeholder('SAFE'),

                TextColumn::make('action')
                    ->label('Ação')
                    ->badge(),

                TextColumn::make('note')
                    ->label('Observação')
                    ->wrap()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('occurrence.student.classroom');

        /** @var User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('professor')) {
            return $query->whereHas('occurrence.student.classroom', function (Builder $classrooms) use ($user): void {
                $classrooms->where('teacher_id', $user->id)
                    ->orWhereHas('teachers', fn (Builder $teachers) => $teachers->whereKey($user->id));
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOccurrenceAudits::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole(['admin', 'aqv', 'professor', 'portaria']) ?? false;
    }
}
