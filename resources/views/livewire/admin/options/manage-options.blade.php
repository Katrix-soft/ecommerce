<div>
    <section class="rounded-lg bg-white shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Opciones</h2>
            <button wire:click="create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded">
                <i class="fa-solid fa-plus mr-2"></i> Nuevo
            </button>
        </div>

        <div class="space-y-6">
            @foreach ($options as $option)
                <div class="border-l-4 border-gray-300 pl-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold">{{ $option->name }}</h3>
                        <div class="flex items-center gap-2">
                            @if ($editing === $option->id)
                                <button wire:click="update" class="text-green-600" title="Guardar">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                                <button wire:click="cancelEdit" class="text-gray-600" title="Cancelar">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            @else
                                <button wire:click="edit({{ $option->id }})" class="text-gray-500 hover:text-gray-700" title="Editar">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <button wire:click="delete({{ $option->id }})" class="text-red-500 hover:text-red-700" title="Eliminar"
                                    wire:confirm="¿Estás seguro de eliminar esta opción?">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    @if ($editing === $option->id)
                        <div class="flex gap-4 mb-3">
                            <div>
                                <x-label class="mb-1">Nombre</x-label>
                                <x-input wire:model="name" />
                                <x-input-error for="name" />
                            </div>
                            <div>
                                <x-label class="mb-1">Tipo</x-label>
                                <select wire:model="type"
                                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="1">Texto</option>
                                    <option value="2">Color</option>
                                </select>
                                <x-input-error for="type" />
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-2">
                        @if ($option->type == '2')
                            {{-- Tipo Color: mostrar círculos de color --}}
                            @foreach ($option->features as $feature)
                                <div class="w-8 h-8 rounded-full border-2 border-gray-300 shadow-sm cursor-default"
                                    style="background-color: {{ $feature->value }};"
                                    title="{{ $feature->description }}">
                                </div>
                            @endforeach
                        @else
                            {{-- Tipo Texto: mostrar badges --}}
                            @foreach ($option->features as $feature)
                                <span class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white dark:bg-gray-800 dark:text-gray-300">
                                    {{ $feature->description }}
                                </span>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $options->links() }}
        </div>
    </section>

    {{-- Modal Crear Opción --}}
    <x-dialog-modal wire:model.live="openModal">
        <x-slot name="title">
            Crear Opción
        </x-slot>
        <x-slot name="content">
            {{-- Nombre y Tipo --}}
            <div class="grid grid-cols-2 gap-6 mb-4">
                <div>
                    <x-label class="mb-1">Nombre</x-label>
                    <x-input wire:model="form.name" class="w-full" placeholder="Ej: Talle, Color, Sexo" />
                    <x-input-error for="form.name" class="mt-1" />
                </div>
                <div>
                    <x-label class="mb-1">Tipo</x-label>
                    <select wire:model.live="form.type"
                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="1">Texto</option>
                        <option value="2">Color</option>
                    </select>
                    <x-input-error for="form.type" class="mt-1" />
                </div>
            </div>

            {{-- Separador Valores --}}
            <div class="flex items-center mb-4">
                <hr class="flex-1 border-gray-300 dark:border-gray-700">
                <span class="px-3 text-sm text-gray-500 dark:text-gray-400 font-medium">Valores</span>
                <hr class="flex-1 border-gray-300 dark:border-gray-700">
            </div>

            {{-- Lista de features agregados --}}
            @if (count($form->features) > 0)
                <div class="mb-4 space-y-2">
                    @foreach ($form->features as $index => $feature)
                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 rounded-lg px-4 py-2 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                @if ($form->type == '2')
                                    <div class="w-6 h-6 rounded-full border-2 border-gray-300 shadow-sm"
                                        style="background-color: {{ $feature['value'] }};"></div>
                                @else
                                    <span class="px-2 py-0.5 text-xs bg-indigo-100 text-indigo-700 rounded font-mono">{{ $feature['value'] }}</span>
                                @endif
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $feature['description'] }}</span>
                            </div>
                            <button wire:click="removeFeature({{ $index }})" class="text-red-400 hover:text-red-600 transition" title="Quitar">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <x-input-error for="form.features" class="mb-4" />
            @endif

            {{-- Formulario para agregar feature --}}
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <x-label class="mb-1">Valor</x-label>
                        @if ($form->type == '2')
                            <input type="color" wire:model="value"
                                class="w-full h-10 rounded-md border border-gray-300 cursor-pointer" />
                        @else
                            <x-input wire:model="value" class="w-full" placeholder="Ej: S, M, L, XL" />
                        @endif
                        <x-input-error for="value" class="mt-1" />
                    </div>
                    <div>
                        <x-label class="mb-1">Descripción</x-label>
                        <x-input wire:model="description" class="w-full" placeholder="Ej: Pequeño, Mediano" />
                        <x-input-error for="description" class="mt-1" />
                    </div>
                </div>
                <button wire:click="addFeature" type="button"
                    class="w-full px-4 py-2 border-2 border-dashed border-indigo-300 text-indigo-600 rounded-lg hover:bg-indigo-50 hover:border-indigo-400 transition font-medium text-sm">
                    <i class="fa-solid fa-plus mr-1"></i> Agregar valor
                </button>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex items-center gap-3">
                <button wire:click="$set('openModal', false)" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded transition">
                    Cancelar
                </button>
                <button wire:click="save" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded transition">
                    <i class="fa-solid fa-check mr-1"></i> Crear Opción
                </button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
