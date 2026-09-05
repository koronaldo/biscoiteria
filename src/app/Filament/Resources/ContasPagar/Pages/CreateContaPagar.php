<?php

namespace App\Filament\Resources\ContasPagar\Pages;

use App\Filament\Resources\ContasPagar\ContaPagarResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContaPagar extends CreateRecord
{
    protected static string $resource = ContaPagarResource::class;

    public function canCreateAnother(): bool
    {
        return false;
    }
}
