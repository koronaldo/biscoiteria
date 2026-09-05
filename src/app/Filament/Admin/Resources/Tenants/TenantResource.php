<?php

namespace App\Filament\Admin\Resources\Tenants;

use App\Filament\Admin\Resources\Tenants\Pages\CreateTenant;
use App\Filament\Admin\Resources\Tenants\Pages\EditTenant;
use App\Filament\Admin\Resources\Tenants\Pages\ListTenants;
use App\Models\Tenant;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $slug = 'tenants';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $navigationLabel = 'Clientes (Tenants)';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?string $modelLabel = 'Cliente';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Identificação')->schema([

                TextInput::make('id')
                    ->label('Subdomínio (ID)')
                    ->helperText('Identificador único. Ex: cliente01 → cliente01.seudominio.com.br')
                    ->required()
                    ->alphaDash()
                    ->maxLength(30)
                    ->unique(ignoreRecord: true)
                    ->disabled(fn ($record) => $record !== null),

                TextInput::make('name')
                    ->label('Nome do Cliente')
                    ->required()
                    ->maxLength(100),

            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('id')
                    ->label('Subdomínio')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('name')
                    ->label('Nome do Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('domains.domain')
                    ->label('Domínio')
                    ->badge()
                    ->separator(', '),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Deletar cliente')
                    ->modalDescription('Isso irá APAGAR permanentemente o banco de dados do cliente e todos os seus dados. Esta ação não pode ser desfeita.')
                    ->modalSubmitActionLabel('Sim, deletar tudo'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenants::route('/'),
            'create' => CreateTenant::route('/create'),
            'edit' => EditTenant::route('/{record}/edit'),
        ];
    }
}
