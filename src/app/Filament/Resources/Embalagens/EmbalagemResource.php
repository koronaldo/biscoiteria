<?php

namespace App\Filament\Resources\Embalagens;

use App\Filament\Resources\Embalagens\Pages\CreateEmbalagem;
use App\Filament\Resources\Embalagens\Pages\EditEmbalagem;
use App\Filament\Resources\Embalagens\Pages\ListEmbalagens;
use App\Models\Embalagem;
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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmbalagemResource extends Resource
{
    protected static ?string $model = Embalagem::class;

    protected static ?string $slug = 'embalagens';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $navigationLabel = 'Embalagens';

    protected static ?string $modelLabel = 'Embalagem';

    protected static ?string $pluralModelLabel = 'Embalagens';

    public static function form(Schema $schema): Schema
    {
        $custo = app(CustoEstimadorService::class);

        return $schema->components([

            TextInput::make('descricao')
                ->label('Descrição')
                ->required()
                ->maxLength(150)
                ->columnSpanFull(),

            Repeater::make('itens')
                ->relationship()
                ->label('Materiais da Embalagem')
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
                ->defaultItems(1)
                ->reorderable(false)
                ->columnSpanFull(),

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

                TextColumn::make('itens_count')
                    ->label('Materiais Cadastrados')
                    ->counts('itens')
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
            'index' => ListEmbalagens::route('/'),
            'create' => CreateEmbalagem::route('/create'),
            'edit' => EditEmbalagem::route('/{record}/edit'),
        ];
    }
}
