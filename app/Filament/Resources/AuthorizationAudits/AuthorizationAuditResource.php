<?php

namespace App\Filament\Resources\AuthorizationAudits;

use App\Filament\Resources\AuthorizationAudits\Pages\ListAuthorizationAudits;
use App\Models\AuthorizationAudit;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AuthorizationAuditResource extends Resource
{
    protected static ?string $model = AuthorizationAudit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Histórico de autorizações';

    protected static ?string $modelLabel = 'histórico de autorização';

    protected static ?string $pluralModelLabel = 'histórico de autorizações';

    protected static string|UnitEnum|null $navigationGroup = 'Operação escolar';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('authorization.id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('authorization.student.name')
                    ->label('Aluno')
                    ->searchable(),

                TextColumn::make('authorization.missed_classes')
                    ->label('Aulas perdidas')
                    ->state(fn ($record): string => collect($record->authorization?->missed_classes ?? [])
                        ->map(fn (string $class): string => match ($class) {
                            'class_1' => '1ª',
                            'class_2' => '2ª',
                            'class_3' => '3ª',
                            'class_4' => '4ª',
                            'class_5' => '5ª',
                            default => $class,
                        })
                        ->implode(', '))
                    ->placeholder('-')
                    ->toggleable(),

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

                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('authorization.student.classroom');

        /** @var User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('professor')) {
            return $query->whereHas('authorization', fn (Builder $authorizations) => $authorizations->where('teacher_id', $user->id));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuthorizationAudits::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole(['admin', 'aqv', 'professor', 'portaria']) ?? false;
    }
}
