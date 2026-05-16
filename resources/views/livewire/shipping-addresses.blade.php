<div>
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-truck text-purple-500"></i>
                Direcciones de Envío
            </h2>
            <button wire:click="$set('newAddress', true)" class="text-sm bg-purple-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-purple-700 transition-all shadow-sm">
                Nueva dirección
            </button>
        </div>

        <div class="p-6">
            @if ($newAddress)
                <div class="bg-gray-50 rounded-3xl p-6 mb-8 border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-800">Agregar nueva dirección</h3>
                        <button wire:click="$set('newAddress', false)" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div class="md:col-span-3">
                            <x-label value="Tipo de dirección" />
                            <select wire:model="form.type" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-sm">
                                <option value="Hogar">Hogar</option>
                                <option value="Trabajo">Trabajo</option>
                                <option value="Otro">Otro</option>
                            </select>
                            <x-input-error for="form.type" />
                        </div>

                        <div class="md:col-span-3">
                            <x-label value="Provincia" />
                            <select wire:model.live="form.province" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-sm">
                                <option value="">Selecciona una provincia</option>
                                <option value="Buenos Aires">Buenos Aires</option>
                                <option value="Capital Federal">Capital Federal</option>
                                <option value="Catamarca">Catamarca</option>
                                <option value="Chaco">Chaco</option>
                                <option value="Chubut">Chubut</option>
                                <option value="Córdoba">Córdoba</option>
                                <option value="Corrientes">Corrientes</option>
                                <option value="Entre Ríos">Entre Ríos</option>
                                <option value="Formosa">Formosa</option>
                                <option value="Jujuy">Jujuy</option>
                                <option value="La Pampa">La Pampa</option>
                                <option value="La Rioja">La Rioja</option>
                                <option value="Mendoza">Mendoza</option>
                                <option value="Misiones">Misiones</option>
                                <option value="Neuquén">Neuquén</option>
                                <option value="Río Negro">Río Negro</option>
                                <option value="Salta">Salta</option>
                                <option value="San Juan">San Juan</option>
                                <option value="San Luis">San Luis</option>
                                <option value="Santa Cruz">Santa Cruz</option>
                                <option value="Santa Fe">Santa Fe</option>
                                <option value="Santiago del Estero">Santiago del Estero</option>
                                <option value="Tierra del Fuego">Tierra del Fuego</option>
                                <option value="Tucumán">Tucumán</option>
                            </select>
                            <x-input-error for="form.province" />
                        </div>

                        <div class="md:col-span-4">
                            <x-label value="Localidad / Ciudad" />
                            <div class="relative">
                                <select wire:model.live="form.locality" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-sm disabled:bg-gray-50" {{ empty($localities) ? 'disabled' : '' }}>
                                    <option value="">{{ empty($localities) ? 'Selecciona primero una provincia' : 'Selecciona una localidad' }}</option>
                                    @foreach ($localities as $loc)
                                        <option value="{{ $loc['nombre'] }}">{{ $loc['nombre'] }}</option>
                                    @endforeach
                                </select>
                                <div wire:loading wire:target="form.province" class="absolute right-8 top-2.5">
                                    <i class="fas fa-spinner fa-spin text-purple-500 text-xs"></i>
                                </div>
                            </div>
                            <x-input-error for="form.locality" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label value="Código Postal" />
                            <x-input wire:model="form.zip_code" class="w-full text-sm" placeholder="Ej: 1425" />
                            <x-input-error for="form.zip_code" />
                        </div>

                        <div class="md:col-span-4">
                            <x-label value="Dirección (Calle y número)" />
                            <x-input wire:model="form.address" class="w-full text-sm" placeholder="Ej: Av. Rivadavia 1234" />
                            <x-input-error for="form.address" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label value="Piso / Depto (opcional)" />
                            <x-input wire:model="form.apartment" class="w-full text-sm" placeholder="Ej: 4B" />
                        </div>

                        <div class="md:col-span-3">
                            <x-label value="Barrio (opcional)" />
                            <x-input wire:model="form.district" class="w-full text-sm" placeholder="Nombre del barrio" />
                        </div>

                        <div class="md:col-span-3">
                            <x-label value="Nombre del lugar (opcional)" />
                            <x-input wire:model="form.description" class="w-full text-sm" placeholder="Ej: Casa, Oficina de papá..." />
                        </div>

                        <div class="md:col-span-6">
                            <x-label value="Referencias (opcional)" />
                            <x-input wire:model="form.reference" class="w-full text-sm" placeholder="Ej: Puerta blanca, frente a la plaza..." />
                        </div>

                        <div class="md:col-span-6 border-t border-gray-200 pt-6 mt-2">
                            <h4 class="font-bold text-gray-800 mb-4 italic">¿Quién recibirá el pedido?</h4>
                            <div class="flex gap-6 mb-4">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" wire:model.live="form.receiver" value="1" class="text-purple-600 focus:ring-purple-500 border-gray-300">
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-purple-600 transition-colors italic">Seré yo</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" wire:model.live="form.receiver" value="2" class="text-purple-600 focus:ring-purple-500 border-gray-300">
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-purple-600 transition-colors italic">Otra persona</span>
                                </label>
                            </div>
                        </div>

                        @if ($form->receiver == '2')
                            <div class="animate-fadeIn md:col-span-6">
                                <x-label value="Nombre de quien recibe" />
                                <x-input wire:model="form.contact" class="w-full text-sm" placeholder="Nombre completo de la persona" />
                            </div>
                        @endif

                        <div class="md:col-span-6">
                            <x-label value="Teléfono de contacto" />
                            <x-input wire:model="form.phone" class="w-full text-sm" placeholder="Ej: 11 1234 5678" />
                            <p class="text-[10px] text-gray-400 mt-1">Necesario para coordinar la entrega</p>
                            <x-input-error for="form.phone" />
                        </div>

                        <div class="md:col-span-6 pt-4">
                            <x-button class="w-full justify-center py-3">
                                {{ $form->addressId ? 'Actualizar dirección' : 'Guardar dirección' }}
                            </x-button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($addresses as $addr)
                    <label class="relative block cursor-pointer group">
                        <input type="radio" name="address_id" value="{{ $addr->id }}" class="peer sr-only">
                        
                        <div class="h-full p-5 rounded-2xl border-2 border-gray-100 peer-checked:border-purple-600 peer-checked:bg-purple-50/30 transition-all hover:border-gray-200 shadow-sm">
                            <div class="flex justify-between items-start mb-3">
                                <span class="px-2 py-1 rounded-lg bg-white border border-gray-100 text-[10px] font-black uppercase tracking-wider text-gray-500">
                                    {{ $addr->type }}
                                </span>
                                @if ($addr->is_default)
                                    <span class="text-[10px] text-purple-600 font-bold bg-purple-100 px-2 py-1 rounded-lg">PREDETERMINADA</span>
                                @endif
                            </div>

                            <p class="font-bold text-gray-800 mb-1 capitalize">{{ $addr->description ?: $addr->address }}</p>
                            <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                                <span class="font-bold text-gray-700">{{ $addr->address }} {{ $addr->apartment }}</span><br>
                                {{ $addr->district ? $addr->district . ', ' : '' }}{{ $addr->locality }} ({{ $addr->zip_code }})<br>
                                {{ $addr->province }}
                            </p>

                            <div class="flex items-center gap-2 text-[10px] text-gray-400 border-t border-gray-100 pt-3">
                                <i class="fas fa-user text-[9px]"></i>
                                <span>{{ $addr->contact }}</span>
                                <span class="mx-1">•</span>
                                <i class="fas fa-phone text-[9px]"></i>
                                <span>{{ $addr->phone }}</span>
                                
                                <div class="ml-auto flex gap-3">
                                    <button type="button" wire:click="edit({{ $addr->id }})" class="text-blue-500 hover:text-blue-700 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" 
                                            onclick="confirmDelete({{ $addr->id }})"
                                            class="text-red-400 hover:text-red-600 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="absolute top-4 right-4 opacity-0 peer-checked:opacity-100 transition-opacity">
                            <div class="w-6 h-6 bg-purple-600 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-check text-white text-[10px]"></i>
                            </div>
                        </div>
                    </label>
                @empty
                    @if (!$newAddress)
                        <div class="col-span-2 py-12 text-center bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <i class="fas fa-map-marker-alt text-2xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No tienes direcciones guardadas</p>
                            <button wire:click="$set('newAddress', true)" class="mt-4 text-purple-600 font-bold hover:underline">
                                Agregar mi primera dirección
                            </button>
                        </div>
                    @endif
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-8 flex justify-end">
        <button class="px-10 py-4 bg-purple-600 text-white font-bold rounded-2xl hover:bg-purple-700 transition-all shadow-lg shadow-purple-100 flex items-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
            Continuar al pago
            <i class="fas fa-credit-card"></i>
        </button>
    </div>

    @push('js')
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#7c3aed',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-6 py-3',
                    cancelButton: 'rounded-xl px-6 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.delete(id);
                }
            })
        }
    </script>
    @endpush
</div>
