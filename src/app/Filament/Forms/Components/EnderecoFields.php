<?php

namespace App\Filament\Forms\Components;

use App\Models\Cidade;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Http;

class EnderecoFields
{
    public static function make(): Repeater
    {
        return Repeater::make('enderecos')
            ->relationship()
            ->hiddenLabel()
            ->addActionLabel('Inserir endereço')
            ->itemLabel(fn ($state) => $state['logradouro'] ?? 'Endereço')
            ->schema([

                Grid::make(2)->schema([
                    TextInput::make('cep')
                        ->label('CEP')
                        ->mask('99999-999')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set, $old) {
                            $cep = preg_replace('/\D/', '', (string) $state);

                            if ($cep === $old || strlen($cep) !== 8) {
                                return;
                            }

                            $resp = Http::get("https://viacep.com.br/ws/{$cep}/json");

                            if (! $resp->ok()) {
                                return;
                            }

                            $data = $resp->json();

                            if (isset($data['erro'])) {
                                return;
                            }

                            $set('logradouro', $data['logradouro'] ?? null);
                            $set('bairro', $data['bairro'] ?? null);

                            if (! empty($data['localidade']) && ! empty($data['uf'])) {
                                $cidade = Cidade::where('nome', $data['localidade'])
                                    ->whereHas('estado', fn ($q) => $q->where('sigla', $data['uf']))
                                    ->first();

                                if ($cidade) {
                                    $set('cidade_id', $cidade->id);
                                }
                            }
                        }),
                    Select::make('tipo')
                        ->label('Tipo')
                        ->options([
                            'principal' => 'Principal',
                            'cobranca' => 'Cobrança',
                            'entrega' => 'Entrega',
                        ])
                        ->default('principal'),
                ]),

                TextInput::make('logradouro')
                    ->label('Logradouro')
                    ->required(),

                Grid::make(3)->schema([
                    TextInput::make('numero')
                        ->columnSpan(1)
                        ->label('Número'),

                    TextInput::make('complemento')
                        ->columnSpan(2)
                        ->label('Complemento'),
                ]),

                TextInput::make('bairro')
                    ->label('Bairro'),

                Select::make('cidade_id')
                    ->label('Cidade')
                    ->options(fn () => Cidade::query()->orderBy('nome')->pluck('nome', 'id'))
                    ->searchable()
                    ->required(),

            ])
            ->defaultItems(1)
            ->reorderable()
            ->collapsible();
    }
}
