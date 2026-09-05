<?php

namespace App\Filament\Resources\UnidadeMedidas\Pages;

use App\Filament\Resources\UnidadeMedidas\UnidadeMedidaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnidadeMedidas extends ListRecords
{
    protected static string $resource = UnidadeMedidaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
