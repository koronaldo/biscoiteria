<?php

namespace App\Filament\Resources\UserGroups;

use App\Filament\Resources\UserGroups\Pages\CreateUserGroup;
use App\Filament\Resources\UserGroups\Pages\EditUserGroup;
use App\Filament\Resources\UserGroups\Pages\ListUserGroups;
use App\Filament\Resources\UserGroups\Pages\ManageGroupMenus;
use App\Models\UserGroup;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserGroupResource extends Resource
{
    protected static ?string $model = UserGroup::class;

    protected static ?string $slug = 'user-groups';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Grupos de Usuários';

    protected static ?string $modelLabel = 'Grupo de Usuários';

    protected static ?string $pluralModelLabel = 'Grupos de Usuários';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('descricao')
                ->label('Descrição')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('descricao')
                    ->label('Descrição do Grupo')
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('menus')
                    ->label('Menus')
                    ->icon('heroicon-o-bars-3')
                    ->url(fn (UserGroup $record) => static::getUrl('manage-group-menus', ['record' => $record])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserGroups::route('/'),
            'create' => CreateUserGroup::route('/create'),
            'edit' => EditUserGroup::route('/{record}/edit'),
            'manage-group-menus' => ManageGroupMenus::route('/{record}/menus'),
        ];
    }
}
