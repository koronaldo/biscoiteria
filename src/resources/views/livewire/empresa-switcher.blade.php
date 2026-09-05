<div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.25rem;">

    <x-filament::dropdown placement="bottom-end">

        <x-slot name="trigger">
            <x-filament::button size="sm" color="gray" icon="heroicon-o-building-office">
                {{ session('nome_empresa') ?? 'Selecionar Empresa' }}
            </x-filament::button>
        </x-slot>

        @forelse($this->empresas as $empresa)
            @php $empresaAtual = session('empresa_id') == $empresa->id; @endphp

            <x-filament::dropdown.list.item
                wire:click="trocarEmpresa({{ $empresa->id }})"
                x-on:click="close()">

                <div class="flex flex-col">
                    <span class="text-sm font-medium">
                        {{ $empresa->entidade->nome ?? "Empresa {$empresa->id}" }}
                    </span>
                    <span class="text-xs text-gray-500">
                        <i style="font-size:10px">{{ $empresa->entidade->documento }}</i>
                    </span>
                </div>

                @if($empresaAtual)
                    <span class="text-primary-600 text-xs font-bold">✓ Atual</span>
                @endif

            </x-filament::dropdown.list.item>
        @empty
            <x-filament::dropdown.list.item disabled>
                <span class="text-xs text-gray-400">Nenhuma empresa vinculada</span>
            </x-filament::dropdown.list.item>
        @endforelse

    </x-filament::dropdown>
</div>
