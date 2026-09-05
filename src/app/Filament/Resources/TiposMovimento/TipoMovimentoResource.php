<?php

namespace App\Filament\Resources\TiposMovimento;

use App\Filament\Resources\TiposMovimento\Pages\CreateTipoMovimento;
use App\Filament\Resources\TiposMovimento\Pages\EditTipoMovimento;
use App\Filament\Resources\TiposMovimento\Pages\ListTiposMovimento;
use App\Models\TipoMovimento;
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

class TipoMovimentoResource extends Resource
{
    protected static ?string $model = TipoMovimento::class;

    protected static ?string $slug = 'tipos-movimento';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Tipos de Movimento';

    protected static ?string $modelLabel = 'Tipo de Movimento';

    protected static ?string $pluralModelLabel = 'Tipos de Movimento';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('descricao')
                ->label('Descrição')
                ->required()
                ->maxLength(100),

            Select::make('tipo')
                ->label('Tipo')
                ->options([
                    'entrada' => 'Entrada',
                    'saida' => 'Saída',
                ])
                ->required(),
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

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'entrada' ? 'Entrada' : 'Saída')
                    ->color(fn (string $state): string => $state === 'entrada' ? 'success' : 'danger')
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
            'index' => ListTiposMovimento::route('/'),
            'create' => CreateTipoMovimento::route('/create'),
            'edit' => EditTipoMovimento::route('/{record}/edit'),
        ];
    }
}
