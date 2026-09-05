<?php

namespace App\Filament\Resources\Fornecedores;

use App\Filament\Forms\Components\EntidadeFields;
use App\Filament\Resources\Fornecedores\Pages\CreateFornecedor;
use App\Filament\Resources\Fornecedores\Pages\EditFornecedor;
use App\Filament\Resources\Fornecedores\Pages\ListFornecedores;
use App\Models\Fornecedor;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FornecedorResource extends Resource
{
    protected static ?string $model = Fornecedor::class;

    protected static ?string $slug = 'fornecedores';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'Fornecedores';

    protected static ?string $modelLabel = 'Fornecedor';

    protected static ?string $pluralModelLabel = 'Fornecedores';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([

            EntidadeFields::make(),

            Section::make('Dados do fornecedor')
                ->schema([

                    TextInput::make('inscricao_estadual')
                        ->label('Inscrição Estadual'),

                    TextInput::make('inscricao_municipal')
                        ->label('Inscrição Municipal'),

                    TextInput::make('contato')
                        ->label('Contato')
                        ->helperText('Nome da pessoa de contato no fornecedor.'),

                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('entidade.nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('entidade.documento')
                    ->label('Documento')
                    ->searchable(),

                TextColumn::make('contato')
                    ->label('Contato')
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
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
            'index' => ListFornecedores::route('/'),
            'create' => CreateFornecedor::route('/create'),
            'edit' => EditFornecedor::route('/{record}/edit'),
        ];
    }
}
