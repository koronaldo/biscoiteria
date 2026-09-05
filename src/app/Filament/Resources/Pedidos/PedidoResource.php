<?php

namespace App\Filament\Resources\Pedidos;

use App\Filament\Resources\Pedidos\Pages\CreatePedido;
use App\Filament\Resources\Pedidos\Pages\EditPedido;
use App\Filament\Resources\Pedidos\Pages\ListPedidos;
use App\Models\Pedido;
use App\Models\ReceitaPacote;
use App\Models\ReceitaTamanho;
use App\Services\CustoEstimadorService;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $slug = 'pedidos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $navigationLabel = 'Pedidos';

    protected static ?string $modelLabel = 'Pedido';

    protected static ?string $pluralModelLabel = 'Pedidos';

    public static function form(Schema $schema): Schema
    {
        $custo = app(CustoEstimadorService::class);

        return $schema->schema([

            Section::make('Dados do Pedido')
                ->schema([

                    Select::make('cliente_id')
                        ->label('Cliente')
                        ->relationship('cliente', 'id')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->entidade?->nome ?? "Cliente {$record->id}")
                        ->searchable()
                        ->preload()
                        ->required(),

                    DatePicker::make('data_pedido')
                        ->label('Data do Pedido')
                        ->required()
                        ->default(now()),

                    TextInput::make('valor_total')
                        ->label('Valor Total')
                        ->prefix('R$')
                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                        ->default(0)
                        ->live()
                        ->readOnly()
                        ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                        ->helperText('Preenchido automaticamente com a soma dos itens.')
                        ->required(),

                ])
                ->columns(3)
                ->columnSpanFull(),

            Section::make('Itens do Pedido')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('itens')
                        ->relationship()
                        ->hiddenLabel()
                        ->live()
                        ->addActionLabel('Inserir item')
                        ->table([
                            TableColumn::make('Biscoito / Pacote'),
                            TableColumn::make('Quantidade')->width('8rem'),
                            TableColumn::make('Preço Unitário'),
                            TableColumn::make('Custo de Referência'),
                            TableColumn::make('Total'),
                        ])
                        ->schema([

                            Select::make('item_selecionado')
                                ->hiddenLabel()
                                ->options(fn () => self::opcoesDeVenda())
                                ->searchable()
                                ->required()
                                ->live()
                                ->dehydrated(false)
                                ->afterStateHydrated(function (Select $component, Get $get) {
                                    $formaVenda = $get('forma_venda');
                                    $tamanhoId = $get('receita_tamanho_id');
                                    $pacoteId = $get('receita_pacote_id');

                                    if ($formaVenda === 'pacote' && filled($pacoteId)) {
                                        $component->state("pacote-{$pacoteId}");
                                    } elseif (filled($tamanhoId)) {
                                        $component->state("tamanho-{$tamanhoId}");
                                    }
                                })
                                ->afterStateUpdated(function (?string $state, Set $set) {
                                    [$tipo, $id] = str_contains((string) $state, '-')
                                        ? explode('-', $state, 2)
                                        : [null, null];

                                    $set('forma_venda', $tipo === 'pacote' ? 'pacote' : 'unidade');
                                    $set('receita_tamanho_id', $tipo === 'tamanho' ? $id : null);
                                    $set('receita_pacote_id', $tipo === 'pacote' ? $id : null);
                                }),

                            Hidden::make('forma_venda')->default('unidade'),
                            Hidden::make('receita_tamanho_id'),
                            Hidden::make('receita_pacote_id'),

                            TextInput::make('quantidade')
                                ->hiddenLabel()
                                ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                ->default(1)
                                ->required()
                                ->live(onBlur: true)
                                ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::recalcularItem($get, $set)),

                            TextInput::make('preco_unitario')
                                ->hiddenLabel()
                                ->prefix('R$')
                                ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                ->default(0)
                                ->required()
                                ->live(onBlur: true)
                                ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::recalcularItem($get, $set)),

                            Placeholder::make('custo_referencia')
                                ->hiddenLabel()
                                ->content(function (Get $get) use ($custo) {
                                    $formaVenda = $get('forma_venda') ?? 'unidade';
                                    $tamanhoId = $get('receita_tamanho_id');
                                    $pacoteId = $get('receita_pacote_id');

                                    if ($formaVenda === 'pacote' && filled($pacoteId)) {
                                        $pacote = ReceitaPacote::find($pacoteId);
                                        $valor = $pacote ? $custo->custoPacote($pacote) : 0.0;
                                    } elseif (filled($tamanhoId)) {
                                        $tamanho = ReceitaTamanho::find($tamanhoId);
                                        $valor = $tamanho ? $custo->custoUnitarioTamanho($tamanho) : 0.0;
                                    } else {
                                        $valor = 0.0;
                                    }

                                    return $custo->formatarReal($valor);
                                })
                                ->helperText('Custo estimado, apenas referência para definir o preço.')
                                ->alignEnd(),

                            TextInput::make('valor_total')
                                ->hiddenLabel()
                                ->prefix('R$')
                                ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                ->default(0)
                                ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                                ->readOnly(),

                        ])
                        ->defaultItems(1)
                        ->reorderable(false),
                ]),
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected static function opcoesDeVenda(): array
    {
        $opcoes = [];

        foreach (ReceitaTamanho::with('receita')->get() as $tamanho) {
            $opcoes["tamanho-{$tamanho->id}"] = "{$tamanho->descricao_completa} (unidade)";
        }

        foreach (ReceitaPacote::with('receita')->get() as $pacote) {
            $opcoes["pacote-{$pacote->id}"] = "{$pacote->descricao_completa} (pacote)";
        }

        return $opcoes;
    }

    protected static function recalcularItem(Get $get, Set $set): void
    {
        $quantidade = (float) ($get('quantidade') ?? 0);
        $precoUnitario = (float) ($get('preco_unitario') ?? 0);

        $set('valor_total', round($quantidade * $precoUnitario, 2));

        self::recalcularValorTotalPedido($get, $set, '../../itens', '../../valor_total');
    }

    protected static function recalcularValorTotalPedido(Get $get, Set $set, string $itensPath = 'itens', string $valorTotalPath = 'valor_total'): void
    {
        $itens = $get($itensPath) ?? [];

        $total = collect($itens)->sum(fn ($item) => (float) ($item['valor_total'] ?? 0));

        $set($valorTotalPath, round($total, 2));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('cliente.entidade.nome')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('data_pedido')
                    ->label('Data do Pedido')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('valor_total')
                    ->label('Valor Total')
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
            'index' => ListPedidos::route('/'),
            'create' => CreatePedido::route('/create'),
            'edit' => EditPedido::route('/{record}/edit'),
        ];
    }
}
