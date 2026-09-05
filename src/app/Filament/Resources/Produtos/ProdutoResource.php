<?php

namespace App\Filament\Resources\Produtos;

use App\Filament\Resources\Produtos\Pages\CreateProduto;
use App\Filament\Resources\Produtos\Pages\EditProduto;
use App\Filament\Resources\Produtos\Pages\ListProdutos;
use App\Models\Produto;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProdutoResource extends Resource
{
    protected static ?string $model = Produto::class;

    protected static ?string $slug = 'produtos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Produtos';

    protected static ?string $modelLabel = 'Produto';

    protected static ?string $pluralModelLabel = 'Produtos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('descricao')
                ->label('Descrição')
                ->required()
                ->maxLength(150)
                ->columnSpanFull(),

            Select::make('unidade_medida_id')
                ->label('Unidade de Medida')
                ->relationship('unidadeMedida', 'descricao')
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->descricao} ({$record->sigla})")
                ->searchable()
                ->preload(),

            TextInput::make('medida')
                ->label('Medida')
                ->numeric()
                ->default(0)
                ->required(),
        ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('descricao')
                    ->label('Descrição')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('unidadeMedida.sigla')
                    ->label('Unidade')
                    ->placeholder('-'),

                TextColumn::make('medida')
                    ->label('Medida')
                    ->numeric(decimalPlaces: 2)
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
            'index' => ListProdutos::route('/'),
            'create' => CreateProduto::route('/create'),
            'edit' => EditProduto::route('/{record}/edit'),
        ];
    }
}
