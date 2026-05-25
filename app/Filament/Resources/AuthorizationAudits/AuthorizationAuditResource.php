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
        $query = parent::getEloquentQuery()->with('authorization.student.classroom');

        /** @var User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('professor')) {
            return $query->whereHas('authorization.student.classroom', function (Builder $classrooms) use ($user): void {
                $classrooms->where('teacher_id', $user->id)
                    ->orWhereHas('teachers', fn (Builder $teachers) => $teachers->whereKey($user->id));
            });
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
