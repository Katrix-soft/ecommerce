<div>
    <section class="rounded-xl bg-white dark:bg-gray-900 shadow-sm border border-gray-100 dark:border-gray-800 p-8">
       <header class="border-b border-gray-200 px-6 py-2">
        <div class="flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white">Opciones</h1>    
            <button wire:click="create" class="px-6 py-2 bg-[#1a1c24] hover:bg-gray-800 text-white text-xs font-bold rounded-lg tracking-widest transition-all active:scale-95">
                NUEVO
            </button>
        </div>

       </header>
        <div class="p-6">
            @if ($product->options->count())
                <div class="space-y-6">
                    @foreach ($product->options as $option )
                        <div wire:key="option-{{ $option->id }}" class="border border-gray-200 dark:border-gray-700 rounded-xl p-6 pt-10 relative">
                            
                            <div class="absolute -top-4 left-6 flex items-center gap-2 bg-white dark:bg-gray-900 px-2">
                                <button onclick="confirmDelete({{ $option->id }})" class="text-red-500 hover:text-red-700 transition-colors">
                                    <i class="fa-solid fa-trash-can text-xl"></i>
                                </button>
                                <span class="text-lg font-semibold text-gray-800 dark:text-white">{{ $option->name }}</span>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                @php
                                    $features = is_array($option->pivot->feature_id) ? $option->pivot->feature_id : json_decode($option->pivot->feature_id, true);
                                @endphp

                                @foreach ($features as $feature)
                                    <div wire:key="option-feature-{{ $feature['id'] }}" class="inline-flex items-center gap-2 px-3 py-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-600 dark:text-gray-300 shadow-sm">
                                        @switch($option->type)
                                            @case(1) {{-- Texto --}}
                                                <span>{{ $feature['value'] }}</span>
                                                @break
                                            @case(2) {{-- Color --}}
                                                <div class="w-4 h-4 rounded-full border border-gray-200" style="background-color: {{ $feature['value'] }}"></div>
                                                <span class="ml-1">{{ $feature['description'] }}</span>
                                                @break
                                        @endswitch
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex items-center p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <div>
                        <span class="font-medium">Info alert!</span> No hay opciones agregadas todavía.
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Sección de Variantes --}}
    <section class="mt-12 rounded-xl bg-white dark:bg-gray-900 shadow-sm border border-gray-100 dark:border-gray-800 p-8">
        <header class="border-b border-gray-200 px-6 py-2 mb-6">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white">Variantes</h1>
        </header>

        @if ($product->variants->count())
            <ul class="divide-y -my-4">
                @foreach ($product->variants as $item)
                    <li class="py-4 flex items-center">
                        <img src="{{ $item->image }}" class="w-12 h-12 object-cover rounded">

                        <div class="ml-4 flex-1">
                            <p class="divide-x text-sm text-gray-600">
                                @foreach ($item->features as $feature)
                                    <span class="px-3">
                                        {{ $feature->description }}
                                    </span>
                                @endforeach
                            </p>
                        </div>

                        <a href="" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Editar
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="flex items-center p-4 text-sm text-gray-800 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-300" role="alert">
                <i class="fa-solid fa-circle-info me-3 text-lg"></i>
                <div>
                    No se han generado variantes para este producto todavía. Haz clic en <span class="font-bold">"Generar Variantes"</span> arriba.
                </div>
            </div>
        @endif
    </section>

    <x-dialog-modal wire:model="showModal">
        <x-slot name="title">
                Agregar nueva opción
        </x-slot>

        <x-slot name="content">
            <div class="mt-4">
                <x-label class="mb-2 text-gray-700 font-medium">Opción</x-label>
                <x-select class="w-full" wire:model.live="variant.option_id">
                    <option value="" disabled>Seleccione una opción</option>
                    @foreach($this->options as $option)
                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="variant.option_id" />
            </div>

            <div class="flex items-center my-8">
                <hr class="flex-1 border-gray-100 dark:border-gray-800">
                <span class="px-4 text-gray-400 text-sm font-light mx-2">
                    Valores
                </span>
                <hr class="flex-1 border-gray-100 dark:border-gray-800">
            </div>

            <div class="space-y-10">
                @foreach ($variant['features'] as $index => $featureInput)
                    <div class="relative border border-gray-200 dark:border-gray-700 rounded-xl p-6 pt-8" wire:key="feature-{{ $index }}">
                        
                        @if(count($variant['features']) > 1)
                            <button wire:click="removeFeature({{ $index }})" 
                                    class="absolute -top-3 left-4 bg-white dark:bg-gray-900 px-2 text-red-500 hover:text-red-700 transition-colors">
                                <i class="fa-solid fa-trash-can text-lg"></i>
                            </button>
                        @endif

                        <div class="space-y-2">
                            <x-label class="text-gray-700 font-medium">Valores</x-label>
                            <x-select class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" 
                                      wire:model.live="variant.features.{{ $index }}.id">
                                <option value="" disabled>Selecciona un valor</option>
                                @foreach($this->features as $f)
                                    <option value="{{ $f->id }}">{{ $f->value }}</option>
                                @endforeach
                            </x-select>
                            <x-input-error for="variant.features.{{ $index }}.id" />
                            <x-input-error for="variant.features.{{ $index }}.value" />
                            <x-input-error for="variant.features.{{ $index }}.description" />
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-center">
                <button wire:click="addFeature" type="button" 
                        class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-2 transition-all hover:scale-105">
                    <i class="fa-solid fa-circle-plus text-lg"></i>
                    Añadir otra característica
                </button>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showModal', false)" class="rounded-xl">
                Cancelar
            </x-secondary-button>

            <x-button wire:click="save" wire:loading.attr="disabled" wire:target="save" class="ml-3 rounded-xl bg-indigo-600">
                <span wire:loading.remove wire:target="save">Guardar</span>
                <span wire:loading wire:target="save">Guardando...</span>
            </x-button>
        </x-slot>
    </x-dialog-modal>

</div>

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(optionId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminarlo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('deleteOption', optionId);
                }
            })
        }

        window.addEventListener('variant-saved', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Guardado!',
                text: 'La opción se ha guardado correctamente.',
                showConfirmButton: false,
                timer: 1500
            })
        window.addEventListener('variant-generated', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Variantes Generadas!',
                text: 'Se han generado todas las combinaciones posibles correctamente.',
                showConfirmButton: false,
                timer: 2000
            })
        })
    </script>
@endpush
