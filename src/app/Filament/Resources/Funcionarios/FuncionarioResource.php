<?php

namespace App\Filament\Resources\Funcionarios;

use App\Filament\Forms\Components\EntidadeFields;
use App\Filament\Resources\Funcionarios\Pages\CreateFuncionario;
use App\Filament\Resources\Funcionarios\Pages\EditFuncionario;
use App\Filament\Resources\Funcionarios\Pages\ListFuncionarios;
use App\Models\Empresa;
use App\Models\Funcionario;
use App\Models\UserGroup;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FuncionarioResource extends Resource
{
    protected static ?string $model = Funcionario::class;

    protected static ?string $slug = 'funcionarios';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Funcionários';

    protected static ?string $pluralModelLabel = 'Funcionários';

    protected static ?string $modelLabel = 'Funcionário';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([

            EntidadeFields::make(),

            Section::make('Acesso a Empresas')
                ->schema([

                    CheckboxList::make('empresas')
                        ->hiddenLabel()
                        ->relationship(
                            name: 'empresas',
                            titleAttribute: 'id',
                            modifyQueryUsing: fn ($query) => $query->with('entidade')
                        )
                        ->getOptionLabelFromRecordUsing(
                            fn (Empresa $record) => $record->entidade?->nome_social
                                ?? $record->entidade?->nome
                                ?? "Empresa {$record->id}"
                        )
                        ->columns(1)
                        ->bulkToggleable(),

                    Select::make('empresa_id')
                        ->label('Empresa padrão')
                        ->relationship('empresaPadrao', 'id')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->entidade?->nome ?? 'Sem nome')
                        ->searchable()
                        ->preload(),

                ]),

            Section::make('Dados adicionais')
                ->schema([

                    TextInput::make('apelido')
                        ->maxLength(20),

                    TextInput::make('cargo')
                        ->label('Cargo')
                        ->maxLength(60),

                    TextInput::make('rg')
                        ->label('RG'),

                    TextInput::make('valor_salario')
                        ->label('Salário')
                        ->prefix('R$')
                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                        ->required(),

                    Toggle::make('bloqueado')
                        ->default(false),

                    Select::make('user_id')
                        ->label('Usuário')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->helperText('Vincule este funcionário a um usuário para permitir o acesso ao sistema. Se deixado em branco, você poderá optar por criar um usuário automaticamente ao gravar, usando o mesmo nome e e-mail do funcionário.'),

                ])
                ->columns(2),

            Section::make('Grupos de Acesso')
                ->description('Define quais menus este funcionário poderá ver no sistema. Se um usuário ainda não existir, os grupos serão aplicados ao usuário criado automaticamente ao gravar.')
                ->schema([

                    CheckboxList::make('user_groups')
                        ->hiddenLabel()
                        ->options(fn () => UserGroup::pluck('descricao', 'id'))
                        ->afterStateHydrated(function (CheckboxList $component, $record) {
                            if ($record?->user) {
                                $component->state(
                                    $record->user->groups()->pluck('user_groups.id')->toArray()
                                );
                            }
                        })
                        ->columns(2)
                        ->bulkToggleable(),

                ]),
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

                TextColumn::make('cargo')
                    ->label('Cargo'),

                TextColumn::make('user.email')
                    ->label('Usuário')
                    ->placeholder('-'),

            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFuncionarios::route('/'),
            'create' => CreateFuncionario::route('/create'),
            'edit' => EditFuncionario::route('/{record}/edit'),
        ];
    }
}
