<?php

namespace App\Filament\Resources\Funcionarios\Pages;

use App\Filament\Resources\Funcionarios\Concerns\CriaUsuarioParaFuncionario;
use App\Filament\Resources\Funcionarios\FuncionarioResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateFuncionario extends CreateRecord
{
    use CriaUsuarioParaFuncionario;

    protected static string $resource = FuncionarioResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Criar')
                ->keyBindings(['mod+s'])
                ->requiresConfirmation(fn (): bool => $this->userIdEstaVazio())
                ->modalHeading(fn (): ?string => $this->userIdEstaVazio() ? 'Criar usuário de acesso?' : null)
                ->modalDescription(fn (): ?string => $this->userIdEstaVazio()
                    ? 'Nenhum usuário foi vinculado a este funcionário. Deseja criar um usuário de acesso automaticamente, usando o mesmo nome e e-mail cadastrados do funcionário e senha padrão "12345678"?'
                    : null)
                ->modalSubmitActionLabel('Sim, criar usuário')
                ->extraModalFooterActions([
                    Action::make('semUsuario')
                        ->label('Não, salvar sem usuário')
                        ->color('gray')
                        ->action(function () {
                            $this->criarUsuarioAutomatico = false;
                            $this->create();
                        }),
                ])
                ->action(function () {
                    $this->criarUsuarioAutomatico = true;
                    $this->create();
                }),
            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->capturarGruposDeAcesso($data);
    }

    protected function afterCreate(): void
    {
        $this->criarUsuarioSeNecessario();
        $this->sincronizarGruposDeAcesso();
    }
}
