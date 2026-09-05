<x-filament::page>

    <div>
        @foreach($menuTree as $parent)

            <div style="margin-bottom: 0.5rem;">

                <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer;">
                    <input
                        type="checkbox"
                        wire:model.live="data.menus"
                        value="{{ $parent['id'] }}"
                        class="fi-checkbox-input rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500"
                    >
                    <span style="font-weight: 600; font-size: 0.875rem;" class="text-gray-900 dark:text-gray-100 select-none">
                        {{ $parent['title'] }}
                    </span>
                </label>

                @foreach($parent['children'] as $child)
                    <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.375rem 1rem 0.375rem 3rem; border-radius: 0.5rem; cursor: pointer;">
                        <span style="color: #9ca3af; font-size: 0.75rem;">↳</span>
                        <input
                            type="checkbox"
                            wire:model.live="data.menus"
                            value="{{ $child['id'] }}"
                            class="fi-checkbox-input rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500"
                        >
                        <span style="font-size: 0.875rem;" class="text-gray-700 dark:text-gray-300 select-none">
                            {{ $child['title'] }}
                        </span>
                    </label>
                @endforeach

            </div>

            <div style="border-bottom: 1px solid; margin-bottom: 0.5rem;" class="border-gray-100 dark:border-white/5"></div>

        @endforeach
    </div>

    <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
        <x-filament::button
            wire:click="save"
            wire:loading.attr="disabled"
            wire:target="save"
            color="warning"
            icon="heroicon-o-check-circle"
        >
            <span wire:loading.remove wire:target="save">Salvar Menus</span>
            <span wire:loading wire:target="save">Salvando...</span>
        </x-filament::button>
    </div>

</x-filament::page>
