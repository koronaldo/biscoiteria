<?php

namespace App\Filament\Resources\Embalagens\Pages;

use App\Filament\Resources\Embalagens\EmbalagemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmbalagens extends ListRecords
{
    protected static string $resource = EmbalagemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
