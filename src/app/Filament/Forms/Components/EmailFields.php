<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;

class EmailFields
{
    public static function make(): Repeater
    {
        return Repeater::make('emails')
            ->relationship()
            ->hiddenLabel()
            ->addActionLabel('Inserir e-mail')
            ->itemLabel(fn ($state) => $state['email'] ?? 'E-mail')
            ->schema([

                Grid::make(1)->schema([
                    TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->required()
                        ->maxLength(255),
                ]),

                Grid::make(3)->schema([

                    Select::make('tipo')
                        ->label('Tipo')
                        ->options([
                            'principal' => 'Principal',
                            'comercial' => 'Comercial',
                            'financeiro' => 'Financeiro',
                            'suporte' => 'Suporte',
                            'contato' => 'Contato',
                        ])
                        ->default('principal')
                        ->required(),

                    Toggle::make('principal')
                        ->label('Principal')
                        ->inline(false),

                    Toggle::make('verificado')
                        ->label('Verificado')
                        ->inline(false),
                ]),

            ])
            ->defaultItems(1)
            ->reorderable()
            ->collapsible()
            ->cloneable(false);
    }
}
