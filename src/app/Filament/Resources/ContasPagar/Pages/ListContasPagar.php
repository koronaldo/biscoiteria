<?php

namespace App\Filament\Resources\ContasPagar\Pages;

use App\Filament\Resources\ContasPagar\ContaPagarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContasPagar extends ListRecords
{
    protected static string $resource = ContaPagarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
