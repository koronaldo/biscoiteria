<?php

namespace App\Livewire;

use App\Models\Empresa;
use App\Models\Funcionario;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EmpresaSwitcher extends Component
{
    public $empresas = [];

    public $empresaAtual;

    public function mount()
    {
        $funcionario = Funcionario::where('user_id', Auth::id())->first();

        if (! $funcionario) {
            return;
        }

        $this->empresas = $funcionario
            ->empresas()
            ->with('entidade')
            ->get();

        $this->empresaAtual = session('empresa_id');

        if ($this->empresaAtual) {
            $empresa = Empresa::with('entidade')->find($this->empresaAtual);

            if ($empresa) {
                session([
                    'empresa_id' => $empresa->id,
                    'nome_empresa' => $empresa->entidade?->nome ?? "Empresa {$empresa->id}",
                ]);
            }
        }
    }

    public function trocarEmpresa($empresaId)
    {
        $empresa = collect($this->empresas)->firstWhere('id', $empresaId);

        if (! $empresa) {
            return;
        }

        session([
            'empresa_id' => $empresa->id,
            'nome_empresa' => $empresa->entidade?->nome ?? "Empresa {$empresa->id}",
        ]);

        $this->empresaAtual = $empresa->id;

        $this->dispatch('empresa-trocada', empresa: $empresa->id);
    }

    public function render()
    {
        return view('livewire.empresa-switcher', [
            'empresas' => $this->empresas,
        ]);
    }
}
