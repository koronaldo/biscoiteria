<?php

namespace App\Filament\Resources\TiposMovimento\Pages;

use App\Filament\Resources\TiposMovimento\TipoMovimentoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTipoMovimento extends EditRecord
{
    protected static string $resource = TipoMovimentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
