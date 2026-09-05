<?php

namespace App\Filament\Resources\Funcionarios\Concerns;

use App\Models\Funcionario;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait CriaUsuarioParaFuncionario
{
    public bool $criarUsuarioAutomatico = false;

    public array $gruposDeAcessoSelecionados = [];

    protected function userIdEstaVazio(): bool
    {
        return blank($this->form->getRawState()['user_id'] ?? null);
    }

    protected function capturarGruposDeAcesso(array $data): array
    {
        $this->gruposDeAcessoSelecionados = $data['user_groups'] ?? [];
        unset($data['user_groups']);

        return $data;
    }

    protected function sincronizarGruposDeAcesso(): void
    {
        $user = $this->record->fresh()->user;

        if (! $user) {
            return;
        }

        $user->groups()->sync($this->gruposDeAcessoSelecionados);
    }

    protected function criarUsuarioSeNecessario(): void
    {
        if (! $this->criarUsuarioAutomatico) {
            return;
        }

        /** @var Funcionario $funcionario */
        $funcionario = $this->record->fresh(['entidade.emails']);

        if (filled($funcionario->user_id)) {
            return;
        }

        $nome = $funcionario->entidade?->nome ?? 'Funcionário';
        $email = $this->obterEmailDoFuncionario($funcionario, $nome);

        $user = User::create([
            'name' => $nome,
            'email' => $email,
            'password' => Hash::make('12345678'),
        ]);

        $funcionario->update(['user_id' => $user->id]);
        $this->record->refresh();
    }

    protected function obterEmailDoFuncionario(Funcionario $funcionario, string $nome): string
    {
        $emails = $funcionario->entidade?->emails ?? collect();

        $principal = $emails->firstWhere('principal', true);
        $email = $principal?->email ?? $emails->first()?->email;

        if (blank($email)) {
            $slug = Str::slug($nome, '.') ?: 'funcionario';
            $email = "{$slug}@biscoiteria.app";
        }

        return $this->garantirEmailUnico($email);
    }

    protected function garantirEmailUnico(string $email): string
    {
        if (! User::where('email', $email)->exists()) {
            return $email;
        }

        [$local, $dominio] = array_pad(explode('@', $email, 2), 2, 'biscoiteria.app');

        $sufixo = 1;
        do {
            $sufixo++;
            $tentativa = "{$local}+{$sufixo}@{$dominio}";
        } while (User::where('email', $tentativa)->exists());

        return $tentativa;
    }
}
