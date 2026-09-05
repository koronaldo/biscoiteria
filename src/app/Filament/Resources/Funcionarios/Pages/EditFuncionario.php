<?php

namespace App\Filament\Resources\Funcionarios\Pages;

use App\Filament\Resources\Funcionarios\Concerns\CriaUsuarioParaFuncionario;
use App\Filament\Resources\Funcionarios\FuncionarioResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFuncionario extends EditRecord
{
    use CriaUsuarioParaFuncionario;

    protected static string $resource = FuncionarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Salvar')
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
                            $this->save();
                        }),
                ])
                ->action(function () {
                    $this->criarUsuarioAutomatico = true;
                    $this->save();
                }),
            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->capturarGruposDeAcesso($data);
    }

    protected function afterSave(): void
    {
        $this->criarUsuarioSeNecessario();
        $this->sincronizarGruposDeAcesso();
    }
}
