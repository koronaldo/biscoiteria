<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Resources\ActivityLogs\Pages\ViewActivityLog;
use App\Models\ActivityLog;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $slug = 'activity-logs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Logs do Sistema';

    protected static ?string $modelLabel = 'Log';

    protected static ?string $pluralModelLabel = 'Logs do Sistema';

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('log_name')
                ->label('Nome do log')
                ->placeholder('-'),
            TextEntry::make('description')
                ->label('Descrição')
                ->columnSpanFull(),
            TextEntry::make('subject_type')
                ->label('Modelo afetado')
                ->placeholder('-'),
            TextEntry::make('causer.name')
                ->label('Usuário')
                ->placeholder('Sistema'),
            TextEntry::make('created_at')
                ->label('Criado em')
                ->dateTime()
                ->placeholder('-'),
            TextEntry::make('event')
                ->label('Evento')
                ->placeholder('-'),
            TextEntry::make('properties')
                ->label('Dados da ação')
                ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                ->columnSpanFull()
                ->extraAttributes(['style' => 'white-space: pre-wrap; font-family: monospace;']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->label('Nome do Log')
                    ->searchable(),

                TextColumn::make('subject_type')
                    ->label('Tipo de assunto')
                    ->searchable(),

                TextColumn::make('causer.name')
                    ->label('Usuário')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sistema'),

                TextColumn::make('event')
                    ->label('Evento')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                ViewAction::make(),
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
            'index' => ListActivityLogs::route('/'),
            'view' => ViewActivityLog::route('/{record}'),
        ];
    }
}
