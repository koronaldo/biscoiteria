<?php

namespace App\Filament\Resources\Receitas;

use App\Filament\Resources\Receitas\Pages\CreateReceita;
use App\Filament\Resources\Receitas\Pages\EditReceita;
use App\Filament\Resources\Receitas\Pages\ListReceitas;
use App\Models\Receita;
use App\Models\ReceitaTamanho;
use App\Services\CustoEstimadorService;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReceitaResource extends Resource
{
    protected static ?string $model = Receita::class;

    protected static ?string $slug = 'receitas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static ?string $navigationLabel = 'Receitas';

    protected static ?string $modelLabel = 'Receita';

    protected static ?string $pluralModelLabel = 'Receitas';

    public static function form(Schema $schema): Schema
    {
        $custo = app(CustoEstimadorService::class);

        return $schema->schema([

            Section::make('Dados da Receita')
                ->schema([

                    TextInput::make('descricao')
                        ->label('Descrição')
                        ->required()
                        ->maxLength(150),

                    Placeholder::make('valor_estimado')
                        ->label('Valor Estimado da Receita')
                        ->content(fn (Get $get) => $custo->formatarReal($custo->calcularValorEstimado($get('itens') ?? [])))
                        ->alignEnd()
                        ->entryWrapperView('filament-infolists::money-entry-wrapper'),

                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make('Produtos da Receita')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('itens')
                        ->relationship()
                        ->hiddenLabel()
                        ->live()
                        ->addActionLabel('Inserir produto')
                        ->table([
                            TableColumn::make('Produto'),
                            TableColumn::make('Quantidade')->width('8rem'),
                            TableColumn::make('Unidade')->alignCenter(),
                            TableColumn::make('Valor Estimado'),
                        ])
                        ->schema([

                            Select::make('produto_id')
                                ->hiddenLabel()
                                ->relationship('produto', 'descricao')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live(),

                            TextInput::make('quantidade')
                                ->hiddenLabel()
                                ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                ->default(0)
                                ->required()
                                ->live(onBlur: true)
                                ->dehydrateStateUsing(fn ($state) => $state ?? 0),

                            Placeholder::make('unidade_medida')
                                ->hiddenLabel()
                                ->content(fn (Get $get) => $custo->unidadeMedidaDoProduto($get('produto_id')))
                                ->alignCenter(),

                            Placeholder::make('valor_estimado_item')
                                ->hiddenLabel()
                                ->content(function (Get $get) use ($custo) {
                                    $produtoId = $get('produto_id');
                                    $quantidade = (float) ($get('quantidade') ?? 0);

                                    if (blank($produtoId) || $quantidade <= 0) {
                                        return $custo->formatarReal(0);
                                    }

                                    return $custo->formatarReal($custo->custoUltimaCompra((int) $produtoId, $quantidade));
                                })
                                ->alignEnd(),

                        ])
                        ->defaultItems(1)
                        ->reorderable(false),
                ]),

            Section::make('Tamanhos da Receita')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('tamanhos')
                        ->relationship()
                        ->hiddenLabel()
                        ->live()
                        ->addActionLabel('Inserir tamanho')
                        ->table([
                            TableColumn::make('Descrição'),
                            TableColumn::make('Quantidade Produzida')->width('10rem'),
                            TableColumn::make('Valor Unitário Estimado'),
                        ])
                        ->schema([

                            TextInput::make('descricao')
                                ->hiddenLabel()
                                ->placeholder('Ex.: Pequeno, Médio, Grande')
                                ->required()
                                ->maxLength(100),

                            TextInput::make('quantidade_produzida')
                                ->hiddenLabel()
                                ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                ->default(0)
                                ->required()
                                ->live(onBlur: true)
                                ->dehydrateStateUsing(fn ($state) => $state ?? 0),

                            Placeholder::make('valor_unitario_estimado')
                                ->hiddenLabel()
                                ->content(function (Get $get) use ($custo) {
                                    $total = $custo->calcularValorEstimado($get('../../itens') ?? []);
                                    $quantidadeProduzida = (float) ($get('quantidade_produzida') ?? 0);

                                    if ($quantidadeProduzida <= 0) {
                                        return $custo->formatarReal(0);
                                    }

                                    return $custo->formatarReal($total / $quantidadeProduzida);
                                })
                                ->alignEnd(),

                        ])
                        ->defaultItems(1)
                        ->reorderable(false),
                ]),

            Section::make('Pacotes da Receita')
                ->description('Um pacote sempre parte de um dos tamanhos cadastrados acima. Salve a receita depois de cadastrar os tamanhos para poder vinculá-los aqui.')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('pacotes')
                        ->relationship()
                        ->hiddenLabel()
                        ->live()
                        ->addActionLabel('Inserir pacote')
                        ->itemLabel(fn (array $state): ?string => $state['descricao'] ?? null)
                        ->schema([

                            TextInput::make('descricao')
                                ->label('Descrição do Pacote')
                                ->placeholder('Ex.: Pacote ZipLock 15x22 com 20 unidades')
                                ->required()
                                ->maxLength(100)
                                ->columnSpan(1),

                            Select::make('receita_tamanho_id')
                                ->label('Tamanho do Biscoito')
                                ->relationship(
                                    name: 'tamanho',
                                    titleAttribute: 'descricao',
                                    modifyQueryUsing: fn ($query) => $query->when(
                                        request()->route('record'),
                                        fn ($q, $receitaId) => $q->where('receita_id', is_object($receitaId) ? $receitaId->getKey() : $receitaId)
                                    ),
                                )
                                ->required()
                                ->live(),

                            TextInput::make('quantidade_por_pacote')
                                ->label('Unidades no Pacote')
                                ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                ->default(0)
                                ->required()
                                ->live(onBlur: true)
                                ->dehydrateStateUsing(fn ($state) => $state ?? 0),

                            Placeholder::make('custo_pacote')
                                ->label('Custo de Produção do Pacote (biscoitos + embalagem)')
                                ->content(fn (Get $get) => $custo->formatarReal(self::custoPacoteAoVivo($get, $custo)))
                                ->alignEnd()
                                ->entryWrapperView('filament-infolists::money-entry-wrapper')
                                ->columnSpanFull(),

                            Repeater::make('embalagens')
                                ->relationship()
                                ->label('Materiais de Embalagem do Pacote')
                                ->live()
                                ->addActionLabel('Inserir material')
                                ->table([
                                    TableColumn::make('Produto'),
                                    TableColumn::make('Quantidade')->width('8rem'),
                                    TableColumn::make('Unidade')->alignCenter(),
                                    TableColumn::make('Valor Estimado'),
                                ])
                                ->schema([

                                    Select::make('produto_id')
                                        ->hiddenLabel()
                                        ->relationship('produto', 'descricao')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->live(),

                                    TextInput::make('quantidade')
                                        ->hiddenLabel()
                                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                        ->default(0)
                                        ->required()
                                        ->live(onBlur: true)
                                        ->dehydrateStateUsing(fn ($state) => $state ?? 0),

                                    Placeholder::make('unidade_medida')
                                        ->hiddenLabel()
                                        ->content(fn (Get $get) => $custo->unidadeMedidaDoProduto($get('produto_id')))
                                        ->alignCenter(),

                                    Placeholder::make('valor_estimado_item')
                                        ->hiddenLabel()
                                        ->content(function (Get $get) use ($custo) {
                                            $produtoId = $get('produto_id');
                                            $quantidade = (float) ($get('quantidade') ?? 0);

                                            if (blank($produtoId) || $quantidade <= 0) {
                                                return $custo->formatarReal(0);
                                            }

                                            return $custo->formatarReal($custo->custoUltimaCompra((int) $produtoId, $quantidade));
                                        })
                                        ->alignEnd(),

                                ])
                                ->defaultItems(0)
                                ->reorderable(false)
                                ->columnSpanFull(),

                            Placeholder::make('valor_lucro')
                                ->label(fn () => '(+) Lucro ('.$custo->formatarPercentual((float) ($custo->empresaAtual()?->percentual_lucro ?? 0)).')')
                                ->content(fn (Get $get) => $custo->formatarReal($custo->detalharVenda(self::custoPacoteAoVivo($get, $custo))['valor_lucro']))
                                ->alignEnd()
                                ->entryWrapperView('filament-infolists::money-entry-wrapper'),

                            Placeholder::make('valor_mao_de_obra')
                                ->label(fn () => '(+) Mão de Obra ('.$custo->formatarPercentual((float) ($custo->empresaAtual()?->percentual_maodeobra ?? 0)).')')
                                ->content(fn (Get $get) => $custo->formatarReal($custo->detalharVenda(self::custoPacoteAoVivo($get, $custo))['valor_mao_de_obra']))
                                ->alignEnd()
                                ->entryWrapperView('filament-infolists::money-entry-wrapper'),

                            Placeholder::make('valor_despesa_fixa')
                                ->label(fn () => '(+) Despesa Fixa ('.$custo->formatarPercentual((float) ($custo->empresaAtual()?->percentual_despesa_fixa ?? 0)).')')
                                ->content(fn (Get $get) => $custo->formatarReal($custo->detalharVenda(self::custoPacoteAoVivo($get, $custo))['valor_despesa_fixa']))
                                ->alignEnd()
                                ->entryWrapperView('filament-infolists::money-entry-wrapper'),

                            Placeholder::make('valor_venda')
                                ->label('Valor de Venda Sugerido')
                                ->content(fn (Get $get) => $custo->formatarReal($custo->detalharVenda(self::custoPacoteAoVivo($get, $custo))['valor_venda']))
                                ->weight('bold')
                                ->color('success')
                                ->alignEnd()
                                ->entryWrapperView('filament-infolists::money-entry-wrapper')
                                ->columnSpanFull(),

                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->reorderable(false),
                ]),
        ]);
    }

    protected static function custoPacoteAoVivo(Get $get, CustoEstimadorService $custo): float
    {
        $tamanhoId = $get('receita_tamanho_id');
        $quantidadePorPacote = (float) ($get('quantidade_por_pacote') ?? 0);

        $custoUnitario = 0.0;

        if (filled($tamanhoId)) {
            $tamanho = ReceitaTamanho::find($tamanhoId);

            if ($tamanho) {
                $custoUnitario = round($custo->custoUnitarioTamanho($tamanho), 2);
            }
        }

        $custoMateriais = $custo->calcularValorEstimado($get('embalagens') ?? []);

        return ($custoUnitario * $quantidadePorPacote) + $custoMateriais;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('descricao')
                    ->label('Descrição')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tamanhos_count')
                    ->label('Tamanhos Cadastrados')
                    ->counts('tamanhos')
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
            'index' => ListReceitas::route('/'),
            'create' => CreateReceita::route('/create'),
            'edit' => EditReceita::route('/{record}/edit'),
        ];
    }
}
