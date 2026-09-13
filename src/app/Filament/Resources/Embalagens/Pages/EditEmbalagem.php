<?php

namespace App\Filament\Resources\Embalagens\Pages;

use App\Filament\Resources\Embalagens\EmbalagemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmbalagem extends EditRecord
{
    protected static string $resource = EmbalagemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
