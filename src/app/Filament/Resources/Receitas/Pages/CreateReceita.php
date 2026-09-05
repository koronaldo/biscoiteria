<?php

namespace App\Filament\Resources\Receitas\Pages;

use App\Filament\Resources\Receitas\ReceitaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReceita extends CreateRecord
{
    protected static string $resource = ReceitaResource::class;

    public function canCreateAnother(): bool
    {
        return false;
    }
}
