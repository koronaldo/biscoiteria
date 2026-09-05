<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;

class EntidadeFields
{
    public static function make(): Group
    {
        return Group::make()
            ->relationship('entidade')
            ->schema([

                Section::make('')
                    ->schema([

                        TextInput::make('nome')
                            ->label('Nome')
                            ->columnSpanFull()
                            ->required(),

                        TextInput::make('nome_social')
                            ->label('Nome social')
                            ->columnSpanFull(),

                        TextInput::make('documento')
                            ->label('CPF / CNPJ')
                            ->maxLength(18)
                            ->minLength(11)
                            ->columnSpan(1),

                        TextInput::make('observacoes')
                            ->label('Observações')
                            ->columnSpan(2),

                    ])
                    ->columns(3),

                Section::make('Endereços')
                    ->schema([
                        EnderecoFields::make(),
                    ])
                    ->columns(1),

                Section::make('Telefones')
                    ->schema([
                        TelefoneFields::make(),
                    ])
                    ->columns(1),

                Section::make('Emails')
                    ->schema([
                        EmailFields::make(),
                    ])
                    ->columns(1),

            ]);
    }
}
