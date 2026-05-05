<div>
    <section class="rounded-xl bg-white dark:bg-gray-900 shadow-sm border border-gray-100 dark:border-gray-800 p-8">
        <div class="flex justify-between items-center mb-10">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Opciones</h2>
            <button wire:click="create" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-100 transition-all active:scale-95">
                <i class="fa-solid fa-plus mr-2"></i>
                Nuevo
            </button>
        </div>

        <div class="space-y-12">
            @foreach ($options as $option)
                <div class="relative pb-10 border-b border-gray-100 dark:border-gray-800 last:border-0 last:pb-0">
                    {{-- Título de la Opción --}}
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ $option->name }}</h3>
                        
                        {{-- Botones de Control (Sutiles) --}}
                        <div class="flex items-center gap-2">
                            @if ($editing === $option->id)
                                <button wire:click="update" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Guardar">
                                    <i class="fa-solid fa-check text-sm"></i>
                                </button>
                                <button wire:click="cancelEdit" class="p-2 text-gray-400 hover:bg-gray-100 rounded-lg transition" title="Cancelar">
                                    <i class="fa-solid fa-xmark text-sm"></i>
                                </button>
                            @else
                                <button wire:click="edit({{ $option->id }})" class="p-2 text-gray-300 hover:text-indigo-500 hover:bg-indigo-50 rounded-lg transition" title="Editar">
                                    <i class="fa-solid fa-edit text-xs"></i>
                                </button>
                                <button type="button" 
                                    @click="Swal.fire({ title: '¿Eliminar opción?', text: 'Se eliminará junto con todos sus valores.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#4f46e5', cancelButtonColor: '#9ca3af', confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar' }).then((result) => { if (result.isConfirmed) { $wire.delete({{ $option->id }}) } })"
                                    class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Eliminar">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Formulario de Edición Rápida --}}
                    @if ($editing === $option->id)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Nombre</label>
                                <input type="text" wire:model="name" class="w-full bg-white border border-gray-200 rounded-lg p-3 text-sm focus:ring-1 focus:ring-indigo-400 focus:border-indigo-400 outline-none" />
                                <x-input-error for="name" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Tipo</label>
                                <select wire:model="type" class="w-full bg-white border border-gray-200 rounded-lg p-3 text-sm focus:ring-1 focus:ring-indigo-400 focus:border-indigo-400 outline-none">
                                    <option value="1">Texto</option>
                                    <option value="2">Color</option>
                                </select>
                                <x-input-error for="type" />
                            </div>
                        </div>
                    @endif

                    {{-- Lista de Valores (Badges/Círculos) --}}
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        @if ($option->type == '2')
                            {{-- Colores --}}
                            @foreach ($option->features as $feature)
                                <button type="button" 
                                    @click="Swal.fire({ title: '¿Eliminar color?', text: 'Se quitará este color de la opción.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#4f46e5', cancelButtonColor: '#9ca3af', confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar' }).then((result) => { if (result.isConfirmed) { $wire.deleteFeature({{ $feature->id }}) } })"
                                    class="relative w-7 h-7 rounded-full border border-gray-200 shadow-sm group overflow-hidden ring-1 ring-transparent hover:ring-red-500 hover:border-red-500 transition-all"
                                    title="{{ $feature->description }}">
                                    
                                    {{-- Color original --}}
                                    <div class="absolute inset-0 transition-transform group-hover:scale-110" style="background-color: {{ $feature->value }};"></div>
                                    
                                    {{-- Capa roja con icono al pasar el mouse --}}
                                    <div class="absolute inset-0 bg-red-500/90 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fa-solid fa-xmark text-white text-[10px] font-bold"></i>
                                    </div>
                                </button>
                            @endforeach
                        @else
                            {{-- Textos --}}
                            @foreach ($option->features as $feature)
                                <button type="button" 
                                    @click="Swal.fire({ title: '¿Eliminar valor?', text: 'Se quitará este valor de la opción.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#4f46e5', cancelButtonColor: '#9ca3af', confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar' }).then((result) => { if (result.isConfirmed) { $wire.deleteFeature({{ $feature->id }}) } })"
                                    class="relative group px-3.5 py-1 text-sm font-medium border border-gray-300 rounded-lg bg-white text-gray-600 hover:bg-red-500 hover:text-white hover:border-red-500 transition-colors shadow-sm">
                                    
                                    <span class="group-hover:opacity-0 transition-opacity">{{ strtolower($feature->description) }}</span>
                                    
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity gap-1.5">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </div>
                                </button>
                            @endforeach
                        @endif
                    </div>

                    {{-- Componente para agregar nuevos valores --}}
                    @livewire('admin.options.add-new-feature', ['option' => $option], key('add-feature-' . $option->id))
                </div>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $options->links() }}
        </div>
    </section>

    {{-- Modal Crear Opción --}}
    <x-dialog-modal wire:model.live="openModal">
        <x-slot name="title">
            <span class="text-xl font-bold">Crear Nueva Opción</span>
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div>
                    <x-label class="mb-1.5 font-medium text-gray-700">Nombre</x-label>
                    <x-input wire:model="form.name" class="w-full" placeholder="Ej: Talle, Color" />
                    <x-input-error for="form.name" />
                </div>
                <div>
                    <x-label class="mb-1.5 font-medium text-gray-700">Tipo</x-label>
                    <select wire:model.live="form.type" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500">
                        <option value="1">Texto</option>
                        <option value="2">Color</option>
                    </select>
                    <x-input-error for="form.type" />
                </div>
            </div>

            <div class="relative flex items-center py-4 mb-4">
                <div class="flex-grow border-t border-gray-100"></div>
                <span class="flex-shrink mx-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Valores</span>
                <div class="flex-grow border-t border-gray-100"></div>
            </div>

            {{-- Lista temporal en Modal --}}
            @if (count($form->features) > 0)
                <div class="mb-6 space-y-2">
                    @foreach ($form->features as $index => $feature)
                        <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-2.5 border border-gray-100">
                            <div class="flex items-center gap-3">
                                @if ($form->type == '2')
                                    <div class="w-6 h-6 rounded-full border border-gray-200" style="background-color: {{ $feature['value'] }};"></div>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold bg-indigo-600 text-white rounded">{{ $feature['value'] }}</span>
                                @endif
                                <span class="text-sm font-medium text-gray-600">{{ $feature['description'] }}</span>
                            </div>
                            <button wire:click="removeFeature({{ $index }})" class="text-gray-400 hover:text-red-500">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="bg-gray-50/50 rounded-xl p-5 border border-gray-100">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-label class="mb-1 text-xs font-bold text-gray-400 uppercase tracking-widest">Valor</x-label>
                        @if ($form->type == '2')
                            <div class="relative" onclick="document.getElementById('modal-picker').click()">
                                <input type="text" readonly value="{{ $value ?: 'Seleccione un color' }}" class="w-full bg-white border border-gray-200 rounded-lg p-2.5 text-sm cursor-pointer" />
                                <div class="absolute right-3 top-2.5 w-6 h-5 rounded border border-gray-100" style="background-color: {{ $value ?: '#000000' }}"></div>
                                <input type="color" id="modal-picker" wire:model.live="value" class="absolute opacity-0 w-0 h-0">
                            </div>
                        @else
                            <x-input wire:model="value" class="w-full" placeholder="Valor" />
                        @endif
                    </div>
                    <div>
                        <x-label class="mb-1 text-xs font-bold text-gray-400 uppercase tracking-widest">Descripción</x-label>
                        <x-input wire:model="description" class="w-full" placeholder="Descripción" />
                    </div>
                </div>
                <button wire:click="addFeature" type="button" class="w-full py-2.5 border-2 border-dashed border-indigo-100 text-indigo-500 rounded-xl hover:bg-white hover:border-indigo-300 transition-all text-xs font-bold uppercase tracking-widest">
                    + Agregar valor
                </button>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex items-center gap-3">
                <button wire:click="$set('openModal', false)" class="px-6 py-2 text-sm font-bold text-gray-400 hover:text-gray-600">Cancelar</button>
                <button wire:click="save" class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg active:scale-95 transition-all">Crear Opción</button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
