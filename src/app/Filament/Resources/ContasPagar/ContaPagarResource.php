<?php

namespace App\Filament\Resources\ContasPagar;

use App\Filament\Resources\ContasPagar\Pages\CreateContaPagar;
use App\Filament\Resources\ContasPagar\Pages\EditContaPagar;
use App\Filament\Resources\ContasPagar\Pages\ListContasPagar;
use App\Models\ContaPagar;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContaPagarResource extends Resource
{
    protected static ?string $model = ContaPagar::class;

    protected static ?string $slug = 'contas-pagar';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Contas a Pagar';

    protected static ?string $modelLabel = 'Conta a Pagar';

    protected static ?string $pluralModelLabel = 'Contas a Pagar';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([

            Section::make('Dados da Conta')
                ->schema([

                    Select::make('fornecedor_id')
                        ->label('Fornecedor')
                        ->relationship('fornecedor', 'id')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->entidade?->nome ?? "Fornecedor {$record->id}")
                        ->searchable()
                        ->preload(),

                    Select::make('tipo_movimento_id')
                        ->label('Tipo de Movimento')
                        ->relationship('tipoMovimento', 'descricao')
                        ->searchable()
                        ->preload(),

                    TextInput::make('descricao')
                        ->label('Descrição')
                        ->maxLength(255)
                        ->helperText('Use para identificar contas avulsas, sem fornecedor ou compra vinculados.'),

                    DatePicker::make('data_vencimento')
                        ->label('Vencimento')
                        ->required(),

                    TextInput::make('valor')
                        ->label('Valor')
                        ->prefix('R$')
                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                        ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                        ->required(),

                ])
                ->columns(2),

            Section::make('Pagamento')
                ->schema([

                    Toggle::make('pago')
                        ->label('Pago')
                        ->live(),

                    DatePicker::make('data_pagamento')
                        ->label('Data do Pagamento')
                        ->visible(fn (Get $get) => (bool) $get('pago')),

                    TextInput::make('valor_pago')
                        ->label('Valor Pago')
                        ->prefix('R$')
                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                        ->visible(fn (Get $get) => (bool) $get('pago')),

                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('descricaoExibicao')
                    ->label('Descrição')
                    ->searchable(query: fn ($query, string $search) => $query
                        ->where('descricao', 'like', "%{$search}%")
                    )
                    ->wrap(),

                TextColumn::make('compra.numero_nota')
                    ->label('Nota')
                    ->placeholder('-'),

                TextColumn::make('tipoMovimento.descricao')
                    ->label('Tipo de Movimento')
                    ->placeholder('-'),

                TextColumn::make('data_vencimento')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('valor')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),

                IconColumn::make('pago')
                    ->label('Pago')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('data_pagamento')
                    ->label('Pago em')
                    ->date('d/m/Y')
                    ->placeholder('-'),

            ])
            ->defaultSort('data_vencimento')
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
            'index' => ListContasPagar::route('/'),
            'create' => CreateContaPagar::route('/create'),
            'edit' => EditContaPagar::route('/{record}/edit'),
        ];
    }
}
