<?php

namespace App\Filament\Resources\UnidadeMedidas;

use App\Filament\Resources\UnidadeMedidas\Pages\CreateUnidadeMedida;
use App\Filament\Resources\UnidadeMedidas\Pages\EditUnidadeMedida;
use App\Filament\Resources\UnidadeMedidas\Pages\ListUnidadeMedidas;
use App\Models\UnidadeMedida;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UnidadeMedidaResource extends Resource
{
    protected static ?string $model = UnidadeMedida::class;

    protected static ?string $slug = 'unidade-medidas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?string $navigationLabel = 'Unidades de Medida';

    protected static ?string $modelLabel = 'Unidade de Medida';

    protected static ?string $pluralModelLabel = 'Unidades de Medida';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('descricao')
                ->label('Descrição')
                ->required()
                ->maxLength(100),

            TextInput::make('sigla')
                ->label('Sigla')
                ->required()
                ->maxLength(10),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('descricao')
                    ->label('Descrição')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sigla')
                    ->label('Sigla')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('descricao')
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
            'index' => ListUnidadeMedidas::route('/'),
            'create' => CreateUnidadeMedida::route('/create'),
            'edit' => EditUnidadeMedida::route('/{record}/edit'),
        ];
    }
}
