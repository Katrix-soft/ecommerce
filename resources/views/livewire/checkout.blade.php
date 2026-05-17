<div>
    <!-- Wizard Stepper -->
    <div class="max-w-4xl mx-auto mb-10 px-4">
        <div class="relative flex items-center justify-between">
            <!-- Progress Line Background -->
            <div class="absolute left-0 right-0 top-1/2 h-1 bg-gray-100 -translate-y-1/2 rounded-full z-0"></div>
            <!-- Dynamic Progress Line -->
            <div class="absolute left-0 top-1/2 h-1 bg-purple-600 -translate-y-1/2 rounded-full transition-all duration-500 z-0" 
                 style="width: {{ $step == 1 ? '16%' : ($step == 2 ? '50%' : '100%') }}"></div>

            <!-- Step 1 Indicator -->
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ $step >= 1 ? 'bg-purple-600 text-white ring-4 ring-purple-100' : 'bg-gray-100 text-gray-400' }}">
                    @if($step > 1)
                        <i class="fas fa-check"></i>
                    @else
                        1
                    @endif
                </div>
                <span class="text-xs font-bold mt-2 {{ $step >= 1 ? 'text-purple-600' : 'text-gray-400' }}">Envío</span>
            </div>

            <!-- Step 2 Indicator -->
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ $step >= 2 ? 'bg-purple-600 text-white ring-4 ring-purple-100' : 'bg-gray-100 text-gray-400' }}">
                    @if($step > 2)
                        <i class="fas fa-check"></i>
                    @else
                        2
                    @endif
                </div>
                <span class="text-xs font-bold mt-2 {{ $step >= 2 ? 'text-purple-600' : 'text-gray-400' }}">Pago</span>
            </div>

            <!-- Step 3 Indicator -->
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ $step >= 3 ? 'bg-purple-600 text-white ring-4 ring-purple-100' : 'bg-gray-100 text-gray-400' }}">
                    3
                </div>
                <span class="text-xs font-bold mt-2 {{ $step >= 3 ? 'text-purple-600' : 'text-gray-400' }}">Confirmación</span>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if ($step == 3)
            <!-- STEP 3: SUCCESS & CONFIRMATION -->
            <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-12 animate-fadeIn print:shadow-none print:border-none">
                <!-- Banner Header -->
                <div class="bg-gradient-to-r from-purple-600 to-indigo-700 py-12 px-8 text-center text-white relative overflow-hidden print:bg-none print:text-black">
                    <!-- Subtle background patterns -->
                    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
                    
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-md shadow-inner animate-bounce">
                        <i class="fas fa-check text-4xl text-white"></i>
                    </div>
                    <h2 class="text-3xl font-black mb-2">¡Pago Confirmado!</h2>
                    <p class="text-purple-100 text-sm font-medium">Gracias por tu compra. Tu pedido está en camino.</p>
                </div>

                <div class="p-8">
                    <!-- Receipt Layout Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-b border-gray-100 pb-8 mb-8">
                        <div>
                            <h3 class="text-xs font-black uppercase text-gray-400 tracking-wider mb-2">Detalles del Pedido</h3>
                            <p class="text-lg font-bold text-gray-800 mb-1">Pedido #{{ str_pad($createdOrder->id, 6, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-xs text-gray-500">Fecha: {{ $createdOrder->created_at->format('d/m/Y H:i') }} hs</p>
                            <p class="text-xs text-gray-500 mt-1">Método de pago: 
                                <span class="font-semibold uppercase text-purple-600">
                                    {{ $createdOrder->payment_method === 'credit_card' ? 'Tarjeta de Crédito (Aprobado)' : ($createdOrder->payment_method === 'bank_transfer' ? 'Transferencia Bancaria' : 'Efectivo / Contraentrega') }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-xs font-black uppercase text-gray-400 tracking-wider mb-2">Dirección de Entrega</h3>
                            <p class="text-sm font-bold text-gray-800">{{ $createdOrder->shipping_address['contact'] }}</p>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                {{ $createdOrder->shipping_address['address'] }} {{ $createdOrder->shipping_address['apartment'] }}<br>
                                {{ $createdOrder->shipping_address['district'] ? $createdOrder->shipping_address['district'] . ', ' : '' }}{{ $createdOrder->shipping_address['locality'] }} ({{ $createdOrder->shipping_address['zip_code'] }})<br>
                                {{ $createdOrder->shipping_address['province'] }}
                            </p>
                            <p class="text-xs text-gray-500 mt-2 font-medium"><i class="fas fa-phone mr-1"></i> {{ $createdOrder->shipping_address['phone'] }}</p>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <h3 class="text-xs font-black uppercase text-gray-400 tracking-wider mb-4">Productos</h3>
                    <div class="space-y-4 mb-8">
                        @foreach ($createdOrder->items as $item)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100/50">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center font-bold text-purple-700">
                                        {{ $item->quantity }}x
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $item->name }}</p>
                                        @if(!empty($item->features))
                                            <p class="text-[10px] text-gray-400 mt-0.5">
                                                @foreach($item->features as $key => $val)
                                                    <span class="capitalize">{{ $key }}: {{ $val }}</span>{{ !$loop->last ? ' | ' : '' }}
                                                @endforeach
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <span class="font-extrabold text-gray-800">${{ number_format($item->price * $item->quantity, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Totals Box -->
                    <div class="bg-purple-50/50 border border-purple-100 rounded-3xl p-6 mb-8">
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-bold">${{ number_format($createdOrder->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Costo de envío</span>
                                <span class="text-green-600 font-bold italic">Gratis</span>
                            </div>
                            <div class="pt-4 border-t border-purple-100 flex justify-between items-center">
                                <span class="font-black text-gray-800 text-lg">Total</span>
                                <span class="text-2xl font-black text-purple-600">${{ number_format($createdOrder->total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-between items-center print:hidden">
                        <button onclick="window.print()" class="w-full sm:w-auto px-6 py-3 border-2 border-purple-200 text-purple-600 font-bold rounded-2xl hover:bg-purple-50 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-print"></i> Imprimir Comprobante
                        </button>
                        <a href="{{ route('welcome.index') }}" class="w-full sm:w-auto px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-purple-200 text-center flex items-center justify-center gap-2">
                            Volver a la Tienda <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

        @else
            <!-- STEPS 1 & 2: MAIN WORKSPACE -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                <!-- Form Area -->
                <div class="lg:col-span-2 space-y-8">
                    @if ($step == 1)
                        <!-- STEP 1: SHIPPING ADDRESSES -->
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-purple-500"></i>
                                    Dirección de Envío
                                </h2>
                                @if (!$newAddress)
                                    <button wire:click="$set('newAddress', true)" class="text-xs bg-purple-600 text-white px-4 py-2.5 rounded-xl font-bold hover:bg-purple-700 transition-all shadow-sm active:scale-95">
                                        <i class="fas fa-plus mr-1"></i> Nueva dirección
                                    </button>
                                @endif
                            </div>

                            <div class="p-6">
                                @if ($newAddress)
                                    <!-- Add New Address Form -->
                                    <div class="bg-gray-50/50 rounded-3xl p-6 mb-8 border border-gray-100">
                                        <div class="flex justify-between items-center mb-6">
                                            <h3 class="font-bold text-gray-800 text-base">{{ $form->addressId ? 'Editar Dirección' : 'Agregar Nueva Dirección' }}</h3>
                                            <button wire:click="$set('newAddress', false)" class="w-8 h-8 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors shadow-sm">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>

                                        <form wire:submit.prevent="saveAddress" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                            <div class="md:col-span-3">
                                                <x-label value="Tipo de dirección" />
                                                <select wire:model="form.type" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-sm mt-1">
                                                    <option value="Hogar">Hogar</option>
                                                    <option value="Trabajo">Trabajo</option>
                                                    <option value="Otro">Otro</option>
                                                </select>
                                                <x-input-error for="form.type" />
                                            </div>

                                            <div class="md:col-span-3">
                                                <x-label value="Provincia" />
                                                <select wire:model.live="form.province" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-sm mt-1">
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
                                                <div class="relative mt-1">
                                                    <select wire:model.live="form.locality" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-sm disabled:bg-gray-50" {{ empty($localities) ? 'disabled' : '' }}>
                                                        <option value="">{{ empty($localities) ? 'Selecciona primero una provincia' : 'Selecciona una localidad' }}</option>
                                                        @foreach ($localities as $loc)
                                                            <option value="{{ $loc['nombre'] }}">{{ $loc['nombre'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div wire:loading wire:target="form.province" class="absolute right-8 top-3">
                                                        <i class="fas fa-spinner fa-spin text-purple-500 text-xs"></i>
                                                    </div>
                                                </div>
                                                <x-input-error for="form.locality" />
                                            </div>

                                            <div class="md:col-span-2">
                                                <x-label value="Código Postal" />
                                                <x-input wire:model="form.zip_code" class="w-full text-sm mt-1 rounded-xl" placeholder="Ej: 1425" />
                                                <x-input-error for="form.zip_code" />
                                            </div>

                                            <div class="md:col-span-4">
                                                <x-label value="Dirección (Calle y número)" />
                                                <x-input wire:model="form.address" class="w-full text-sm mt-1 rounded-xl" placeholder="Ej: Av. Rivadavia 1234" />
                                                <x-input-error for="form.address" />
                                            </div>

                                            <div class="md:col-span-2">
                                                <x-label value="Piso / Depto (opcional)" />
                                                <x-input wire:model="form.apartment" class="w-full text-sm mt-1 rounded-xl" placeholder="Ej: 4B" />
                                            </div>

                                            <div class="md:col-span-3">
                                                <x-label value="Barrio (opcional)" />
                                                <x-input wire:model="form.district" class="w-full text-sm mt-1 rounded-xl" placeholder="Nombre del barrio" />
                                            </div>

                                            <div class="md:col-span-3">
                                                <x-label value="Nombre del lugar (opcional)" />
                                                <x-input wire:model="form.description" class="w-full text-sm mt-1 rounded-xl" placeholder="Ej: Casa, Oficina..." />
                                            </div>

                                            <div class="md:col-span-6">
                                                <x-label value="Referencias (opcional)" />
                                                <x-input wire:model="form.reference" class="w-full text-sm mt-1 rounded-xl" placeholder="Ej: Puerta blanca, frente a la plaza..." />
                                            </div>

                                            <div class="md:col-span-6 border-t border-gray-100 pt-6 mt-2">
                                                <h4 class="font-bold text-gray-800 text-sm mb-3">¿Quién recibirá el pedido?</h4>
                                                <div class="flex gap-6 mb-4">
                                                    <label class="flex items-center gap-2 cursor-pointer group">
                                                        <input type="radio" wire:model.live="form.receiver" value="1" class="text-purple-600 focus:ring-purple-500 border-gray-300">
                                                        <span class="text-sm font-medium text-gray-700 group-hover:text-purple-600 transition-colors">Seré yo</span>
                                                    </label>
                                                    <label class="flex items-center gap-2 cursor-pointer group">
                                                        <input type="radio" wire:model.live="form.receiver" value="2" class="text-purple-600 focus:ring-purple-500 border-gray-300">
                                                        <span class="text-sm font-medium text-gray-700 group-hover:text-purple-600 transition-colors">Otra persona</span>
                                                    </label>
                                                </div>
                                            </div>

                                            @if ($form->receiver == '2')
                                                <div class="animate-fadeIn md:col-span-6">
                                                    <x-label value="Nombre de quien recibe" />
                                                    <x-input wire:model="form.contact" class="w-full text-sm mt-1 rounded-xl" placeholder="Nombre completo" />
                                                </div>
                                            @endif

                                            <div class="md:col-span-6">
                                                <x-label value="Teléfono de contacto" />
                                                <x-input wire:model="form.phone" class="w-full text-sm mt-1 rounded-xl" placeholder="Ej: 11 1234 5678" />
                                                <x-input-error for="form.phone" />
                                            </div>

                                            <div class="md:col-span-6 pt-4 flex gap-4">
                                                <button type="button" wire:click="$set('newAddress', false)" class="flex-1 py-3 text-sm font-bold border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition-all">
                                                    Cancelar
                                                </button>
                                                <button type="submit" class="flex-1 py-3 text-sm font-bold bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition-all shadow-md shadow-purple-100">
                                                    {{ $form->addressId ? 'Actualizar' : 'Guardar dirección' }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                <!-- Addresses List -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @forelse ($addresses as $addr)
                                        <label class="relative block cursor-pointer group">
                                            <input type="radio" name="selectedAddressId" value="{{ $addr->id }}" 
                                                   wire:model.live="selectedAddressId" class="peer sr-only">
                                            
                                            <div class="h-full p-5 rounded-2xl border-2 border-gray-100 peer-checked:border-purple-600 peer-checked:bg-purple-50/20 transition-all hover:border-gray-200 shadow-sm relative z-1">
                                                <div class="flex justify-between items-start mb-3">
                                                    <span class="px-2 py-0.5 rounded-lg bg-gray-100 text-[10px] font-black uppercase text-gray-500">
                                                        {{ $addr->type }}
                                                    </span>
                                                    @if ($addr->is_default)
                                                        <span class="text-[10px] text-purple-600 font-bold bg-purple-100/50 px-2 py-0.5 rounded-lg">PREDETERMINADA</span>
                                                    @endif
                                                </div>

                                                <p class="font-bold text-gray-800 mb-1 capitalize">{{ $addr->description ?: $addr->address }}</p>
                                                <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                                                    <span class="font-semibold text-gray-700">{{ $addr->address }} {{ $addr->apartment }}</span><br>
                                                    {{ $addr->district ? $addr->district . ', ' : '' }}{{ $addr->locality }} ({{ $addr->zip_code }})<br>
                                                    {{ $addr->province }}
                                                </p>

                                                <div class="flex items-center gap-2 text-[10px] text-gray-400 border-t border-gray-100 pt-3">
                                                    <i class="fas fa-user text-[9px]"></i>
                                                    <span>{{ $addr->contact }}</span>
                                                    <span class="mx-1">•</span>
                                                    <i class="fas fa-phone text-[9px]"></i>
                                                    <span>{{ $addr->phone }}</span>
                                                    
                                                    <div class="ml-auto flex gap-3 z-10">
                                                        <button type="button" wire:click.stop="edit({{ $addr->id }})" class="text-blue-500 hover:text-blue-700 transition-colors">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" onclick="confirmDeleteAddress({{ $addr->id }})" class="text-red-400 hover:text-red-600 transition-colors">
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
                                            <div class="col-span-2 py-16 text-center bg-gray-50/50 rounded-3xl border-2 border-dashed border-gray-200">
                                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                                    <i class="fas fa-map-marker-alt text-2xl text-gray-300"></i>
                                                </div>
                                                <p class="text-gray-500 font-medium">No tienes direcciones registradas</p>
                                                <button wire:click="$set('newAddress', true)" class="mt-4 bg-purple-600 hover:bg-purple-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition-all shadow-sm shadow-purple-100">
                                                    Registrar mi primera dirección
                                                </button>
                                            </div>
                                        @endif
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Continue Button to step 2 -->
                        <div class="flex justify-end pt-4">
                            <button wire:click="goToPayment" class="px-10 py-4 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-purple-100 flex items-center gap-3 active:scale-95">
                                Continuar al pago <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                    @elseif ($step == 2)
                        <!-- STEP 2: PAYMENT METHOD -->
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-credit-card text-purple-500"></i>
                                    Método de Pago
                                </h2>
                            </div>

                            <div class="p-6">
                                <!-- Payment Selector Cards -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                                    <!-- Credit Card selector -->
                                    <label class="block cursor-pointer">
                                        <input type="radio" name="paymentMethod" value="credit_card" 
                                               wire:model.live="paymentMethod" class="peer sr-only">
                                        <div class="p-5 rounded-2xl border-2 border-gray-100 peer-checked:border-purple-600 peer-checked:bg-purple-50/20 text-center hover:border-gray-200 transition-all shadow-sm">
                                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <i class="fas fa-credit-card text-purple-600 text-lg"></i>
                                            </div>
                                            <p class="font-bold text-gray-800 text-sm">Tarjeta de Crédito</p>
                                            <p class="text-[10px] text-gray-400 mt-1">Aprobación instantánea</p>
                                        </div>
                                    </label>

                                    <!-- Bank Transfer selector -->
                                    <label class="block cursor-pointer">
                                        <input type="radio" name="paymentMethod" value="bank_transfer" 
                                               wire:model.live="paymentMethod" class="peer sr-only">
                                        <div class="p-5 rounded-2xl border-2 border-gray-100 peer-checked:border-purple-600 peer-checked:bg-purple-50/20 text-center hover:border-gray-200 transition-all shadow-sm">
                                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <i class="fas fa-university text-purple-600 text-lg"></i>
                                            </div>
                                            <p class="font-bold text-gray-800 text-sm">Transferencia</p>
                                            <p class="text-[10px] text-gray-400 mt-1">Coordinar envío del ticket</p>
                                        </div>
                                    </label>

                                    <!-- Cash on Delivery selector -->
                                    <label class="block cursor-pointer">
                                        <input type="radio" name="paymentMethod" value="cash" 
                                               wire:model.live="paymentMethod" class="peer sr-only">
                                        <div class="p-5 rounded-2xl border-2 border-gray-100 peer-checked:border-purple-600 peer-checked:bg-purple-50/20 text-center hover:border-gray-200 transition-all shadow-sm">
                                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <i class="fas fa-hand-holding-usd text-purple-600 text-lg"></i>
                                            </div>
                                            <p class="font-bold text-gray-800 text-sm">Efectivo / Contraentrega</p>
                                            <p class="text-[10px] text-gray-400 mt-1">Pagás al recibir el pedido</p>
                                        </div>
                                    </label>
                                </div>

                                <!-- Dynamic payment details display -->
                                @if ($paymentMethod === 'credit_card')
                                    <!-- Interactive 3D Flip Credit Card Mockup using AlpineJS -->
                                    <div x-data="{ 
                                            num: @entangle('cardNumber').live, 
                                            name: @entangle('cardName').live, 
                                            exp: @entangle('cardExpiry').live, 
                                            cvv: @entangle('cardCvv').live, 
                                            focused: false 
                                        }" 
                                         class="max-w-md mx-auto mb-10 perspective">
                                        
                                        <!-- Card Container -->
                                        <div class="relative w-full h-56 rounded-2xl shadow-2xl transition-all duration-700 preserve-3d cursor-pointer"
                                             :class="{ 'rotate-y-180': focused }">
                                             
                                            <!-- FRONT OF CARD -->
                                            <div class="absolute inset-0 bg-gradient-to-tr from-purple-700 via-purple-900 to-indigo-900 rounded-2xl p-6 text-white backface-hidden flex flex-col justify-between overflow-hidden">
                                                <!-- Abstract card design shapes -->
                                                <div class="absolute -right-16 -top-16 w-44 h-44 bg-white/5 rounded-full blur-xl"></div>
                                                <div class="absolute -left-12 -bottom-12 w-32 h-32 bg-purple-600/20 rounded-full blur-xl"></div>
                                                
                                                <div class="flex justify-between items-start">
                                                    <!-- Credit Card Chip -->
                                                    <div class="w-12 h-9 bg-yellow-400/90 rounded-md shadow-inner flex flex-col justify-between p-1">
                                                        <div class="border-b border-yellow-600/30 h-1"></div>
                                                        <div class="border-b border-yellow-600/30 h-1"></div>
                                                        <div class="border-b border-yellow-600/30 h-1"></div>
                                                    </div>
                                                    <!-- Brand Logo Indicator -->
                                                    <div>
                                                        <template x-if="num.startsWith('4')">
                                                            <span class="text-2xl font-black italic tracking-widest text-white/90">VISA</span>
                                                        </template>
                                                        <template x-if="num.startsWith('5')">
                                                            <span class="text-2xl font-black italic tracking-widest text-white/90">Mastercard</span>
                                                        </template>
                                                        <template x-if="num.startsWith('3')">
                                                            <span class="text-2xl font-black italic tracking-widest text-white/90">AMEX</span>
                                                        </template>
                                                        <template x-if="!num.startsWith('4') && !num.startsWith('5') && !num.startsWith('3')">
                                                            <i class="fas fa-credit-card text-2xl text-white/60"></i>
                                                        </template>
                                                    </div>
                                                </div>

                                                <!-- Card Number -->
                                                <div class="text-xl font-bold tracking-[0.2em] font-mono py-2 text-center"
                                                     x-text="num ? num.replace(/\s?/g, '').replace(/(\d{4})/g, '$1 ').trim() : '•••• •••• •••• ••••'">
                                                </div>

                                                <div class="flex justify-between items-end">
                                                    <!-- Cardholder Name -->
                                                    <div>
                                                        <p class="text-[9px] text-purple-300 uppercase tracking-widest font-black">Titular de la tarjeta</p>
                                                        <p class="text-sm font-semibold tracking-wider font-mono truncate max-w-[200px]" 
                                                           x-text="name ? name.toUpperCase() : 'NOMBRE COMPLETO'"></p>
                                                    </div>
                                                    <!-- Expiry Date -->
                                                    <div class="text-right">
                                                        <p class="text-[9px] text-purple-300 uppercase tracking-widest font-black">Vence</p>
                                                        <p class="text-sm font-semibold tracking-wider font-mono" 
                                                           x-text="exp ? exp : 'MM/AA'"></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- BACK OF CARD -->
                                            <div class="absolute inset-0 bg-gradient-to-tr from-purple-800 via-indigo-900 to-black rounded-2xl text-white backface-hidden rotate-y-180 flex flex-col justify-between py-6">
                                                <!-- Magnetic strip -->
                                                <div class="w-full h-11 bg-gray-900/90"></div>
                                                
                                                <div class="px-6">
                                                    <p class="text-[8px] text-right text-gray-400 mb-1 font-semibold uppercase tracking-widest">Firma Autorizada</p>
                                                    <div class="flex items-center gap-3">
                                                        <!-- White signature strip -->
                                                        <div class="flex-1 h-9 bg-white/95 rounded-md flex items-center justify-end px-3 font-mono italic text-gray-800 font-bold tracking-widest text-sm select-none" 
                                                             style="background-image: repeating-linear-gradient(45deg, #e5e7eb, #e5e7eb 4px, transparent 4px, transparent 8px)">
                                                             ••••
                                                        </div>
                                                        <!-- CVV Box -->
                                                        <div class="w-16 h-8 bg-yellow-400 text-gray-900 font-black rounded-md flex items-center justify-center font-mono text-sm shadow-inner" 
                                                             x-text="cvv ? cvv : '•••'">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="px-6 flex justify-between items-center">
                                                    <span class="text-[8px] text-gray-400 leading-normal max-w-[180px]">
                                                        Esta tarjeta es de demostración ficticia para el checkout premium de la tienda.
                                                    </span>
                                                    <span class="text-sm font-black italic tracking-widest text-white/40">SECURE</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card Inputs Form -->
                                        <div class="mt-8 space-y-4">
                                            <div>
                                                <x-label value="Número de Tarjeta" />
                                                <x-input type="text" maxlength="19" class="w-full text-sm mt-1 rounded-xl tracking-widest" 
                                                         placeholder="4111 2222 3333 4444" 
                                                         x-model="num"
                                                         x-on:input="num = $event.target.value.replace(/\D/g, '').replace(/(\d{4})/g, '$1 ').trim()" />
                                                <x-input-error for="cardNumber" />
                                            </div>

                                            <div>
                                                <x-label value="Nombre del Titular (como figura en la tarjeta)" />
                                                <x-input type="text" class="w-full text-sm mt-1 rounded-xl capitalize" 
                                                         placeholder="JUAN PEREZ" 
                                                         x-model="name" />
                                                <x-input-error for="cardName" />
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <x-label value="Vencimiento" />
                                                    <x-input type="text" maxlength="5" class="w-full text-sm mt-1 rounded-xl text-center tracking-widest" 
                                                             placeholder="MM/AA" 
                                                             x-model="exp"
                                                             x-on:input="
                                                                let v = $event.target.value.replace(/\D/g, '');
                                                                if(v.length > 2) { v = v.substring(0,2) + '/' + v.substring(2,4); }
                                                                exp = v;
                                                             " />
                                                    <x-input-error for="cardExpiry" />
                                                </div>
                                                <div>
                                                    <x-label value="Código (CVV)" />
                                                    <x-input type="password" maxlength="4" class="w-full text-sm mt-1 rounded-xl text-center tracking-widest" 
                                                             placeholder="123" 
                                                             x-model="cvv"
                                                             x-on:focus="focused = true"
                                                             x-on:blur="focused = false"
                                                             x-on:input="cvv = $event.target.value.replace(/\D/g, '')" />
                                                    <x-input-error for="cardCvv" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @elseif ($paymentMethod === 'bank_transfer')
                                    <!-- Bank Details view -->
                                    <div class="max-w-md mx-auto bg-purple-50/50 border border-purple-100 rounded-3xl p-6 mb-4 animate-fadeIn">
                                        <h3 class="font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                                            <i class="fas fa-university text-purple-600"></i>
                                            Datos de nuestra cuenta bancaria
                                        </h3>
                                        <div class="space-y-4 text-sm text-gray-600">
                                            <div class="flex justify-between border-b border-purple-100/50 pb-2">
                                                <span class="font-medium text-gray-500">Banco</span>
                                                <span class="font-black text-gray-800">Banco Galicia</span>
                                            </div>
                                            <div class="flex justify-between border-b border-purple-100/50 pb-2">
                                                <span class="font-medium text-gray-500">Tipo de cuenta</span>
                                                <span class="font-black text-gray-800">Cuenta Corriente en $</span>
                                            </div>
                                            <div class="flex justify-between border-b border-purple-100/50 pb-2">
                                                <span class="font-medium text-gray-500">CBU</span>
                                                <span class="font-mono font-black text-gray-800 tracking-wider">0070001120000004242421</span>
                                            </div>
                                            <div class="flex justify-between border-b border-purple-100/50 pb-2">
                                                <span class="font-medium text-gray-500">Alias</span>
                                                <span class="font-black text-purple-600 select-all cursor-pointer hover:underline" title="Copiar alias">katrix.ecommerce.mp</span>
                                            </div>
                                            <div class="flex justify-between border-b border-purple-100/50 pb-2">
                                                <span class="font-medium text-gray-500">CUIT</span>
                                                <span class="font-mono font-black text-gray-800">30-71649234-9</span>
                                            </div>
                                            <div class="flex justify-between pb-1">
                                                <span class="font-medium text-gray-500">Titular</span>
                                                <span class="font-black text-gray-800">Katrix Software S.A.</span>
                                            </div>
                                        </div>

                                        <div class="bg-white rounded-2xl p-4 border border-purple-100 mt-6 flex gap-3 items-start">
                                            <i class="fas fa-info-circle text-purple-500 mt-1"></i>
                                            <p class="text-xs text-gray-500 leading-relaxed">
                                                Una vez realizada la transferencia bancaria, por favor envía el comprobante por e-mail a <strong>pagos@katrix.com</strong> indicando tu número de orden en el asunto.
                                            </p>
                                        </div>
                                    </div>

                                @elseif ($paymentMethod === 'cash')
                                    <!-- Cash on delivery info -->
                                    <div class="max-w-md mx-auto bg-gray-50 border border-gray-200 rounded-3xl p-6 mb-4 animate-fadeIn">
                                        <h3 class="font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                                            <i class="fas fa-hand-holding-usd text-purple-600"></i>
                                            Pago contra entrega (Efectivo)
                                        </h3>
                                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                                            ¡Simple y seguro! Pagás el pedido en efectivo al repartidor en el momento en que recibís el paquete en tu puerta.
                                        </p>
                                        <div class="bg-white rounded-2xl p-4 border border-gray-100 flex gap-3 items-start">
                                            <i class="fas fa-exclamation-triangle text-amber-500 mt-1"></i>
                                            <p class="text-xs text-gray-500 leading-relaxed font-medium">
                                                Asegúrate de contar con el monto exacto de <strong>${{ Cart::instance('shopping')->total() }}</strong> en efectivo al momento de la entrega para agilizar el cambio y la entrega de tu pedido.
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Buttons Navigation for step 2 -->
                        <div class="flex justify-between pt-4">
                            <button wire:click="backToShipping" class="px-8 py-4 border-2 border-purple-200 hover:bg-purple-50 text-purple-600 font-bold rounded-2xl transition-all flex items-center gap-2 active:scale-95">
                                <i class="fas fa-arrow-left"></i> Volver a envío
                            </button>
                            <button wire:click="placeOrder" 
                                    wire:loading.attr="disabled"
                                    class="px-10 py-4 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-purple-100 flex items-center gap-3 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="placeOrder">Confirmar Pedido <i class="fas fa-check"></i></span>
                                <span wire:loading wire:target="placeOrder" class="flex items-center gap-2">
                                    <i class="fas fa-spinner fa-spin"></i> Procesando...
                                </span>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Sticky Order Summary Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sticky top-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Resumen</h3>
                        
                        <!-- List items in cart -->
                        <div class="max-h-56 overflow-y-auto mb-6 pr-2 space-y-4 custom-scrollbar">
                            @foreach (Cart::instance('shopping')->content() as $item)
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center font-bold text-xs text-purple-600 border border-gray-100/50">
                                        {{ $item->qty }}x
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-gray-800 truncate" title="{{ $item->name }}">{{ $item->name }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">${{ number_format($item->price, 2) }} c/u</p>
                                    </div>
                                    <span class="text-xs font-black text-gray-800">${{ number_format($item->price * $item->qty, 2) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Price Breakdown -->
                        <div class="space-y-3 pt-6 border-t border-gray-50 mb-6">
                            <div class="flex justify-between text-gray-500 text-xs font-medium">
                                <span>Subtotal</span>
                                <span class="font-bold text-gray-800">${{ Cart::instance('shopping')->subtotal() }}</span>
                            </div>
                            <div class="flex justify-between text-gray-500 text-xs font-medium">
                                <span>Envío</span>
                                <span class="text-green-600 font-extrabold italic">¡GRATIS!</span>
                            </div>
                            <div class="pt-4 border-t border-gray-50 flex justify-between items-center">
                                <span class="font-black text-gray-800 text-sm">Total</span>
                                <span class="text-xl font-black text-purple-600">${{ Cart::instance('shopping')->total() }}</span>
                            </div>
                        </div>

                        <!-- Sidebar Tips -->
                        <div class="bg-gray-50 rounded-2xl p-4 flex items-start gap-3">
                            <i class="fas fa-shield-alt text-purple-500 mt-0.5 text-sm"></i>
                            <p class="text-[10px] text-gray-500 leading-relaxed">
                                Compra 100% protegida. Cumplimos con los estándares de seguridad de datos de la industria bancaria.
                             </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Print & Custom Card Styling -->
    <style>
        .perspective {
            perspective: 1000px;
        }
        .preserve-3d {
            transform-style: preserve-3d;
        }
        .backface-hidden {
            backface-visibility: hidden;
        }
        .rotate-y-180 {
            transform: rotateY(180deg);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f9fafb;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #d1d5db;
        }
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .print\:hidden {
                display: none !important;
            }
            .print\:shadow-none {
                box-shadow: none !important;
            }
            .print\:border-none {
                border: none !important;
            }
        }
    </style>

    <!-- SweetAlert Address Deletion Handler -->
    @push('js')
    <script>
        function confirmDeleteAddress(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta dirección se eliminará permanentemente de tu cuenta.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#7c3aed',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'rounded-3xl p-6',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-bold',
                    cancelButton: 'rounded-xl px-5 py-2.5 font-bold'
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
