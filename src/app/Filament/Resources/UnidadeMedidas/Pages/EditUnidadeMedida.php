<?php

namespace App\Filament\Resources\UnidadeMedidas\Pages;

use App\Filament\Resources\UnidadeMedidas\UnidadeMedidaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUnidadeMedida extends EditRecord
{
    protected static string $resource = UnidadeMedidaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
