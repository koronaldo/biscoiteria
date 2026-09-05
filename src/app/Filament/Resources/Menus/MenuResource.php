<?php

namespace App\Filament\Resources\Menus;

use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Models\Menu;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $slug = 'menus';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static ?string $navigationLabel = 'Menus';

    protected static ?string $modelLabel = 'Menu';

    protected static ?string $pluralModelLabel = 'Menus';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Título')
                ->required(),

            TextInput::make('icon')
                ->label('Ícone')
                ->helperText('Ex: heroicon-o-folder-open'),

            TextInput::make('route')
                ->label('Rota'),

            TextInput::make('permission')
                ->label('Permissão'),

            Select::make('parent_id')
                ->label('Menu Pai')
                ->relationship(name: 'parent', titleAttribute: 'title')
                ->searchable()
                ->preload()
                ->nullable(),

            TextInput::make('sort')
                ->label('Ordem')
                ->required()
                ->numeric()
                ->default(0),

            Toggle::make('active')
                ->label('Ativo')
                ->default(true)
                ->required(),

            Toggle::make('admin')
                ->label('Admin')
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('icon')
                    ->label('Ícone'),

                TextColumn::make('route')
                    ->label('Rota')
                    ->searchable(),

                TextColumn::make('parent.title')
                    ->label('Menu Pai'),

                TextColumn::make('sort')
                    ->label('Ordem')
                    ->sortable(),

                IconColumn::make('active')
                    ->label('Ativo')
                    ->boolean(),

                IconColumn::make('admin')
                    ->label('Admin')
                    ->boolean(),
            ])
            ->defaultSort('sort')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}
