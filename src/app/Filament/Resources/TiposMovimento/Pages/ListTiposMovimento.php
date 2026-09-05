<?php

namespace App\Filament\Resources\TiposMovimento\Pages;

use App\Filament\Resources\TiposMovimento\TipoMovimentoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTiposMovimento extends ListRecords
{
    protected static string $resource = TipoMovimentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
