<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;

class TelefoneFields
{
    public static function make(): Repeater
    {
        return Repeater::make('telefones')
            ->relationship()
            ->hiddenLabel()
            ->addActionLabel('Inserir telefone')
            ->itemLabel(fn ($state) => $state['numero'] ?? 'Telefone')
            ->schema([

                Grid::make(2)->schema([

                    TextInput::make('numero')
                        ->label('Número')
                        ->required()
                        ->mask('(99) 99999-9999'),

                    Select::make('tipo')
                        ->label('Tipo')
                        ->options([
                            'principal' => 'Principal',
                            'comercial' => 'Comercial',
                            'celular' => 'Celular',
                            'financeiro' => 'Financeiro',
                            'suporte' => 'Suporte',
                        ])
                        ->default('principal')
                        ->required(),

                ]),

                Grid::make(2)->schema([

                    Toggle::make('whatsapp')
                        ->label('WhatsApp')
                        ->inline(false),

                    Toggle::make('principal')
                        ->label('Principal')
                        ->inline(false),

                ]),

            ])
            ->defaultItems(1)
            ->reorderable()
            ->collapsible()
            ->cloneable(false);
    }
}
