<?php

namespace App\Filament\Resources\Compras;

use App\Filament\Resources\Compras\Pages\CreateCompra;
use App\Filament\Resources\Compras\Pages\EditCompra;
use App\Filament\Resources\Compras\Pages\ListCompras;
use App\Models\Compra;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompraResource extends Resource
{
    protected static ?string $model = Compra::class;

    protected static ?string $slug = 'compras';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $navigationLabel = 'Compras';

    protected static ?string $modelLabel = 'Compra';

    protected static ?string $pluralModelLabel = 'Compras';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([

            Section::make('Dados da Compra')
                ->schema([

                    Select::make('fornecedor_id')
                        ->label('Fornecedor')
                        ->relationship('fornecedor', 'id')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->entidade?->nome ?? "Fornecedor {$record->id}")
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('tipo_movimento_id')
                        ->label('Tipo de Movimento')
                        ->relationship(
                            name: 'tipoMovimento',
                            titleAttribute: 'descricao',
                            modifyQueryUsing: fn ($query) => $query->where('tipo', 'entrada'),
                        )
                        ->searchable()
                        ->preload(),

                    TextInput::make('numero_nota')
                        ->label('Número da Nota')
                        ->required()
                        ->maxLength(30),

                    DatePicker::make('data_emissao')
                        ->label('Data de Emissão')
                        ->required(),

                    DatePicker::make('data_compra')
                        ->label('Data da Compra')
                        ->required()
                        ->default(now()),

                    TextInput::make('valor_nota')
                        ->label('Valor da Nota')
                        ->prefix('R$')
                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                        ->default(0)
                        ->live()
                        ->readOnly()
                        ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                        ->helperText('Preenchido automaticamente com a soma dos produtos.')
                        ->required(),

                ])
                ->columns(3)
                ->columnSpanFull(),

            Tabs::make('detalhes')
                ->columnSpanFull()
                ->tabs([

                    Tab::make('Produtos')
                        ->schema([
                            Repeater::make('itens')
                                ->relationship()
                                ->hiddenLabel()
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::recalcularValorNota($get, $set))
                                ->addActionLabel('Inserir produto')
                                ->table([
                                    TableColumn::make('Produto'),
                                    TableColumn::make('Quantidade'),
                                    TableColumn::make('Valor Unitário'),
                                    TableColumn::make('Desconto'),
                                    TableColumn::make('Acréscimo'),
                                    TableColumn::make('Total'),
                                ])
                                ->schema([

                                    Select::make('produto_id')
                                        ->hiddenLabel()
                                        ->relationship('produto', 'descricao')
                                        ->searchable()
                                        ->preload()
                                        ->required(),

                                    TextInput::make('quantidade')
                                        ->hiddenLabel()
                                        ->numeric()
                                        ->default(1)
                                        ->required()
                                        ->live(onBlur: true)
                                        ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                                        ->afterStateUpdated(fn (Get $get, Set $set) => self::recalcularItem($get, $set)),

                                    TextInput::make('valor_unitario')
                                        ->hiddenLabel()
                                        ->prefix('R$')
                                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                        ->default(0)
                                        ->required()
                                        ->live(onBlur: true)
                                        ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                                        ->afterStateUpdated(fn (Get $get, Set $set) => self::recalcularItem($get, $set)),

                                    TextInput::make('valor_desconto')
                                        ->hiddenLabel()
                                        ->prefix('R$')
                                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                        ->default(0)
                                        ->live(onBlur: true)
                                        ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                                        ->afterStateUpdated(fn (Get $get, Set $set) => self::recalcularItem($get, $set)),

                                    TextInput::make('valor_acrescimo')
                                        ->hiddenLabel()
                                        ->prefix('R$')
                                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                        ->default(0)
                                        ->live(onBlur: true)
                                        ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                                        ->afterStateUpdated(fn (Get $get, Set $set) => self::recalcularItem($get, $set)),

                                    TextInput::make('valor_total')
                                        ->hiddenLabel()
                                        ->prefix('R$')
                                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                        ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                                        ->default(0)
                                        ->readOnly(),

                                ])
                                ->defaultItems(1)
                                ->reorderable(false),
                        ]),

                    Tab::make('Parcelamento')
                        ->schema([

                            Grid::make(2)
                                ->schema([

                                    TextInput::make('quantidade_parcelas')
                                        ->label('Quantidade de Parcelas')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->live(onBlur: true)
                                        ->dehydrated(false)
                                        ->afterStateUpdated(fn (Get $get, Set $set) => self::gerarParcelas($get, $set)),

                                    DatePicker::make('data_inicial_parcelas')
                                        ->label('Data da 1ª Parcela')
                                        ->default(now())
                                        ->live()
                                        ->dehydrated(false)
                                        ->afterStateUpdated(fn (Get $get, Set $set) => self::gerarParcelas($get, $set)),

                                ]),

                            Repeater::make('parcelas')
                                ->relationship()
                                ->hiddenLabel()
                                ->addActionLabel('Adicionar parcela')
                                ->table([
                                    TableColumn::make('Nº'),
                                    TableColumn::make('Vencimento'),
                                    TableColumn::make('Valor'),
                                ])
                                ->schema([

                                    TextInput::make('numero_parcela')
                                        ->hiddenLabel()
                                        ->numeric()
                                        ->required(),

                                    DatePicker::make('data_vencimento')
                                        ->hiddenLabel()
                                        ->required(),

                                    TextInput::make('valor')
                                        ->hiddenLabel()
                                        ->prefix('R$')
                                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                        ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                                        ->required(),

                                    Hidden::make('fornecedor_id'),
                                    Hidden::make('tipo_movimento_id'),

                                ])
                                ->defaultItems(0)
                                ->reorderable(false),
                        ]),
                ]),
        ]);
    }

    protected static function recalcularItem(Get $get, Set $set): void
    {
        $quantidade = (float) ($get('quantidade') ?? 0);
        $valorUnitario = (float) ($get('valor_unitario') ?? 0);
        $desconto = (float) ($get('valor_desconto') ?? 0);
        $acrescimo = (float) ($get('valor_acrescimo') ?? 0);

        $total = ($quantidade * $valorUnitario) - $desconto + $acrescimo;

        $set('valor_total', round($total, 2));

        self::recalcularValorNota($get, $set, '../../itens', '../../valor_nota');
    }

    protected static function recalcularValorNota(Get $get, Set $set, string $itensPath = 'itens', string $valorNotaPath = 'valor_nota'): void
    {
        $itens = $get($itensPath) ?? [];

        $total = collect($itens)->sum(fn ($item) => (float) ($item['valor_total'] ?? 0));

        $set($valorNotaPath, round($total, 2));
    }

    protected static function gerarParcelas(Get $get, Set $set): void
    {
        $quantidadeParcelas = (int) ($get('quantidade_parcelas') ?? 0);
        $dataInicial = $get('data_inicial_parcelas');
        $valorNota = (float) ($get('valor_nota') ?? 0);
        $fornecedorId = $get('fornecedor_id');
        $tipoMovimentoId = $get('tipo_movimento_id');

        if ($quantidadeParcelas < 1 || blank($dataInicial)) {
            return;
        }

        $valorParcela = round($valorNota / $quantidadeParcelas, 2);
        $somaParcelas = 0;
        $parcelas = [];
        $data = Carbon::parse($dataInicial);

        for ($i = 1; $i <= $quantidadeParcelas; $i++) {
            if ($i === $quantidadeParcelas) {
                $valor = round($valorNota - $somaParcelas, 2);
            } else {
                $valor = $valorParcela;
                $somaParcelas += $valorParcela;
            }

            $parcelas[] = [
                'numero_parcela' => $i,
                'data_vencimento' => $data->copy()->addMonths($i - 1)->toDateString(),
                'valor' => $valor,
                'fornecedor_id' => $fornecedorId,
                'tipo_movimento_id' => $tipoMovimentoId,
            ];
        }

        $set('parcelas', $parcelas);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('numero_nota')
                    ->label('Nº Nota')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('fornecedor.entidade.nome')
                    ->label('Fornecedor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tipoMovimento.descricao')
                    ->label('Tipo de Movimento')
                    ->placeholder('-'),

                TextColumn::make('data_emissao')
                    ->label('Emissão')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('data_compra')
                    ->label('Compra')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('valor_nota')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),

            ])
            ->defaultSort('id', 'desc')
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
            'index' => ListCompras::route('/'),
            'create' => CreateCompra::route('/create'),
            'edit' => EditCompra::route('/{record}/edit'),
        ];
    }
}
