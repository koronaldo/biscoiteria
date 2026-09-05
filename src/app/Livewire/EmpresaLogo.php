<?php

namespace App\Livewire;

use App\Models\Empresa;
use Livewire\Attributes\On;
use Livewire\Component;

class EmpresaLogo extends Component
{
    public ?string $logo = null;

    public function mount(): void
    {
        $this->carregarLogo();
    }

    #[On('empresa-trocada')]
    public function carregarLogo(): void
    {
        $empresaId = session('empresa_id');
        $this->logo = $empresaId
            ? Empresa::find($empresaId)?->logo
            : null;
    }

    public function render()
    {
        return view('livewire.empresa-logo');
    }
}
