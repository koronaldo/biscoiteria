<?php

namespace App\Filament\Resources\Empresas;

use App\Filament\Forms\Components\EntidadeFields;
use App\Filament\Resources\Empresas\Pages\CreateEmpresa;
use App\Filament\Resources\Empresas\Pages\EditEmpresa;
use App\Filament\Resources\Empresas\Pages\ListEmpresas;
use App\Models\Empresa;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class EmpresaResource extends Resource
{
    protected static ?string $model = Empresa::class;

    protected static ?string $slug = 'empresas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $navigationLabel = 'Empresas';

    protected static ?string $pluralModelLabel = 'Empresas';

    protected static ?string $modelLabel = 'Empresa';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([

            EntidadeFields::make(),

            Section::make('Dados fiscais')
                ->schema([

                    TextInput::make('inscricao_estadual')
                        ->label('Inscrição Estadual'),

                    TextInput::make('inscricao_municipal')
                        ->label('Inscrição Municipal'),

                ])
                ->columns(2),

            Section::make('Precificação')
                ->schema([

                    TextInput::make('percentual_lucro')
                        ->label('% de Lucro')
                        ->suffix('%')
                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                        ->default(0)
                        ->dehydrateStateUsing(fn ($state) => $state ?? 0),

                    TextInput::make('valor_hora_maodeobra')
                        ->label('Valor da Hora de Mão de Obra')
                        ->prefix('R$')
                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                        ->default(0)
                        ->dehydrateStateUsing(fn ($state) => $state ?? 0),

                    TextInput::make('percentual_despesa_fixa')
                        ->label('% de Despesa Fixa')
                        ->suffix('%')
                        ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                        ->default(0)
                        ->dehydrateStateUsing(fn ($state) => $state ?? 0),

                ])
                ->columns(3),

            Section::make('Logotipo')
                ->schema([
                    Placeholder::make('logo_atual')
                        ->label('Logo atual')
                        ->content(fn ($record) => $record?->logo
                            ? new HtmlString('<img src="data:image/png;base64,'.$record->logo.'" style="max-height:80px; max-width:220px; border-radius:6px; border:1px solid #e5e7eb; padding:4px;">')
                            : new HtmlString('<span style="font-size:0.82rem;color:#9ca3af;">Nenhum logo cadastrado</span>')
                        )
                        ->visibleOn('edit'),

                    Actions::make([
                        Action::make('remover_logo')
                            ->label('Remover logo')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Remover logo')
                            ->modalDescription('Tem certeza que deseja remover o logo desta empresa?')
                            ->action(function ($record) {
                                $record->update(['logo' => null]);
                            })
                            ->hidden(fn ($record) => empty($record?->logo)),
                    ])
                        ->visibleOn('edit'),

                    FileUpload::make('logo')
                        ->label('Enviar novo logo (PNG ou JPG, máx. 200 KB)')
                        ->image()
                        ->maxSize(200)
                        ->acceptedFileTypes(['image/png', 'image/jpeg'])
                        ->saveUploadedFileUsing(fn ($file) => base64_encode($file->get()))
                        ->dehydrated(true),
                ])
                ->columns(1),
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

                TextColumn::make('inscricao_estadual')
                    ->label('IE'),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmpresas::route('/'),
            'create' => CreateEmpresa::route('/create'),
            'edit' => EditEmpresa::route('/{record}/edit'),
        ];
    }
}
