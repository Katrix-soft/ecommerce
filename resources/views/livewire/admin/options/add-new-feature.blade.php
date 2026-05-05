<div class="mt-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
        {{-- Campo Valor --}}
        <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Valor</label>
            <div class="relative">
                @if ($option->type == '2')
                    {{-- Input Color Estilo Captura --}}
                    <div class="relative group" onclick="document.getElementById('picker-{{ $option->id }}').click()">
                        <input type="text" readonly 
                            value="{{ $value ?: 'Seleccione un color' }}"
                            class="w-full bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-1 focus:ring-indigo-400 focus:border-indigo-400 block p-3 pr-12 transition-all cursor-pointer placeholder-gray-400" />
                        
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <div class="w-10 h-6 rounded border border-gray-100 shadow-sm" 
                                style="background-color: {{ $value ?: '#000000' }}"></div>
                        </div>
                    </div>
                    <input type="color" id="picker-{{ $option->id }}" wire:model.live="value" class="absolute opacity-0 w-0 h-0">
                @else
                    {{-- Input Texto Estilo Captura --}}
                    <input type="text" wire:model="value" wire:keydown.enter="addFeature"
                        placeholder="Ingrese el valor de la opción"
                        class="w-full bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-1 focus:ring-indigo-400 focus:border-indigo-400 block p-3 transition-all placeholder-gray-300" />
                @endif
                <x-input-error for="value" class="mt-1" />
            </div>
        </div>

        {{-- Campo Descripción --}}
        <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Descripción</label>
            <input type="text" wire:model="description" wire:keydown.enter="addFeature"
                placeholder="Ingrese una descripción"
                class="w-full bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-1 focus:ring-indigo-400 focus:border-indigo-400 block p-3 transition-all placeholder-gray-300" />
            <x-input-error for="description" class="mt-1" />
        </div>
    </div>

    {{-- Botón Agregar (Invisible en captura pero necesario para guardar) --}}
    <div class="flex justify-end mt-3">
        <button wire:click="addFeature" type="button" 
            class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition uppercase tracking-widest flex items-center gap-1 p-1">
            <i class="fa-solid fa-plus"></i>
            Guardar valor
        </button>
    </div>
</div>
