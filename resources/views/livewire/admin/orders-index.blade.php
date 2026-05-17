<div>
    <!-- Header with premium gradient border -->
    <div class="mb-6 bg-gradient-to-r from-purple-500/10 via-indigo-500/10 to-blue-500/10 p-6 rounded-2xl border border-indigo-100 shadow-sm backdrop-blur-md">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-700 to-indigo-800 bg-clip-text text-transparent">Gestión de Órdenes</h1>
        <p class="text-sm text-gray-500 mt-1">Supervisa pedidos, gestiona tickets de despacho y asigna envíos.</p>
    </div>

    <!-- Quick Stats Cards with micro-animations -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Pedidos</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ \App\Models\Order::count() }}</h3>
                </div>
                <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                    <i class="fa-solid fa-receipt text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pendientes</p>
                    <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ \App\Models\Order::where('status', 'pending')->count() }}</h3>
                </div>
                <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                    <i class="fa-solid fa-clock text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Listos para Despacho</p>
                    <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ \App\Models\Ticket::where('status', 'ready_to_ship')->count() }}</h3>
                </div>
                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                    <i class="fa-solid fa-box text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Enviados</p>
                    <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ \App\Models\Order::where('status', 'shipped')->count() }}</h3>
                </div>
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                    <i class="fa-solid fa-truck text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search controls -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" wire:model.live="search" placeholder="Buscar por ID de orden, cliente, email..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200/50 transition duration-200 text-sm">
        </div>
        
        <div class="flex items-center gap-3">
            <select wire:model.live="statusFilter" 
                class="border border-gray-200 rounded-xl px-4 py-2.5 bg-white text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200/50 transition duration-200">
                <option value="">Todos los Estados</option>
                <option value="pending">Pendientes</option>
                <option value="processing">En Proceso</option>
                <option value="shipped">Enviados</option>
                <option value="delivered">Entregados</option>
                <option value="cancelled">Cancelados</option>
            </select>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="py-4 px-6">ID Pedido</th>
                        <th class="py-4 px-6">Cliente</th>
                        <th class="py-4 px-6">Fecha</th>
                        <th class="py-4 px-6">Método de Pago</th>
                        <th class="py-4 px-6 text-center">Estado Pedido</th>
                        <th class="py-4 px-6 text-center">Ticket Despacho</th>
                        <th class="py-4 px-6 text-right">Total</th>
                        <th class="py-4 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-indigo-50/20 transition-all duration-150">
                            <td class="py-4 px-6 font-semibold text-indigo-700">
                                #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium text-gray-900">{{ $order->user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $order->user->email }}</div>
                            </td>
                            <td class="py-4 px-6 text-gray-600">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-4 px-6 text-gray-600 capitalize">
                                @if($order->payment_method === 'credit_card')
                                    <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-credit-card text-blue-500"></i> Tarjeta</span>
                                @elseif($order->payment_method === 'bank_transfer')
                                    <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-university text-indigo-500"></i> Transferencia</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-money-bill-wave text-emerald-500"></i> Efectivo</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @php
                                    $orderBadges = [
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        'processing' => 'bg-blue-50 text-blue-700 border-blue-100',
                                        'shipped' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                        'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    ];
                                    $orderTexts = [
                                        'pending' => 'Pendiente',
                                        'processing' => 'En Proceso',
                                        'shipped' => 'Enviado',
                                        'delivered' => 'Entregado',
                                        'cancelled' => 'Cancelado',
                                    ];
                                    $badgeClass = $orderBadges[$order->status] ?? 'bg-gray-50 text-gray-700';
                                    $orderText = $orderTexts[$order->status] ?? $order->status;
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeClass }}">
                                    {{ $orderText }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($order->ticket)
                                    @php
                                        $ticketBadges = [
                                            'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            'ready_to_ship' => 'bg-purple-50 text-purple-700 border-purple-100 animate-pulse',
                                            'dispatched' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                            'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        ];
                                        $ticketTexts = [
                                            'pending' => 'Por Preparar',
                                            'ready_to_ship' => 'Listo p/ Despachar',
                                            'dispatched' => 'Despachado',
                                            'delivered' => 'Entregado',
                                        ];
                                        $tBadgeClass = $ticketBadges[$order->ticket->status] ?? 'bg-gray-50 text-gray-700';
                                        $tText = $ticketTexts[$order->ticket->status] ?? $order->ticket->status;
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $tBadgeClass }}">
                                        {{ $tText }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 italic">No Generado</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right font-bold text-gray-800">
                                ${{ number_format($order->total, 2) }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="viewOrder({{ $order->id }})" title="Ver Detalles"
                                        class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition duration-150">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    @if($order->ticket && $order->ticket->status === 'pending')
                                        <button wire:click="markAsReady({{ $order->id }})" title="Marcar Listo para Despachar"
                                            class="p-2 text-purple-600 hover:bg-purple-50 rounded-xl transition duration-150">
                                            <i class="fa-solid fa-box-open"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-receipt text-3xl mb-2 text-gray-300"></i>
                                    <span>No se encontraron pedidos.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Detailed Order Modal with backdrop filter -->
    @if($showModal && $selectedOrder)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <!-- Modal panel -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-gray-100">
                    <div class="bg-gradient-to-r from-indigo-700 to-purple-800 px-6 py-4 text-white flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold">Detalle de Pedido #{{ str_pad($selectedOrder->id, 6, '0', STR_PAD_LEFT) }}</h3>
                            <p class="text-xs text-indigo-100 mt-0.5">Realizado el {{ $selectedOrder->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <button wire:click="closeModal" class="text-indigo-100 hover:text-white transition duration-150">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <div class="p-6">
                        <!-- Customer & Shipping info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Información del Cliente</h4>
                                <p class="font-bold text-gray-800">{{ $selectedOrder->user->name }}</p>
                                <p class="text-sm text-gray-500 mt-1"><i class="fa-solid fa-envelope w-4"></i> {{ $selectedOrder->user->email }}</p>
                                @if($selectedOrder->user->dni)
                                    <p class="text-sm text-gray-500 mt-1"><i class="fa-solid fa-id-card w-4"></i> DNI: {{ $selectedOrder->user->dni }} ({{ $selectedOrder->user->document_type ?? 'DNI' }})</p>
                                @endif
                            </div>

                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Dirección de Envío</h4>
                                @php
                                    $addr = $selectedOrder->shipping_address;
                                @endphp
                                @if(is_array($addr))
                                    <p class="font-bold text-gray-800">{{ $addr['contact'] ?? 'Contacto' }} - {{ $addr['phone'] ?? 'Teléfono' }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $addr['address'] }} {{ $addr['apartment'] ? ', '.$addr['apartment'] : '' }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $addr['locality'] }}, {{ $addr['province'] }} (CP: {{ $addr['zip_code'] }})</p>
                                    @if(!empty($addr['reference']))
                                        <p class="text-xs text-indigo-600 mt-1.5 italic"><i class="fa-solid fa-info-circle"></i> Ref: {{ $addr['reference'] }}</p>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-600">{{ $selectedOrder->shipping_address }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Ticket Status & Logistical Actions -->
                        <div class="border-t border-b border-gray-100 py-4 mb-6">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-semibold text-gray-600">Estado del Despacho:</span>
                                    @if($selectedOrder->ticket)
                                        <span class="px-3 py-1 text-xs font-bold rounded-full border bg-indigo-50 text-indigo-700 border-indigo-100">
                                            {{ $selectedOrder->ticket->status === 'pending' ? 'Por Preparar' : ($selectedOrder->ticket->status === 'ready_to_ship' ? 'Listo para Despacho' : ($selectedOrder->ticket->status === 'dispatched' ? 'Despachado' : 'Entregado')) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <!-- Action: Print Ticket -->
                                    <a href="{{ route('admin.orders.print', $selectedOrder->id) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition duration-150">
                                        <i class="fa-solid fa-print"></i> Imprimir Ticket
                                    </a>

                                    @if($selectedOrder->ticket && $selectedOrder->ticket->status === 'pending')
                                        <button wire:click="markAsReady({{ $selectedOrder->id }})"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-purple-600 hover:bg-purple-700 rounded-xl transition duration-150 shadow-sm">
                                            <i class="fa-solid fa-check"></i> Listo p/ Despachar
                                        </button>
                                    @endif

                                    @if($selectedOrder->ticket && $selectedOrder->ticket->status === 'ready_to_ship' && !$selectedOrder->shipment)
                                        <button wire:click="openShipmentForm"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition duration-150 shadow-sm">
                                            <i class="fa-solid fa-truck"></i> Generar Envío
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Shipment allocation form -->
                            @if($showShipmentForm)
                                <div class="mt-4 bg-emerald-50/50 border border-emerald-100 p-4 rounded-2xl animate-fadeIn">
                                    <h5 class="text-xs font-bold text-emerald-800 uppercase tracking-wider mb-2">Asignar Envío a Conductor</h5>
                                    <div class="flex flex-col sm:flex-row items-end gap-3">
                                        <div class="flex-1 w-full">
                                            <label class="block text-xs text-gray-500 mb-1">Selecciona un Conductor disponible:</label>
                                            <select wire:model="selectedDriverId" 
                                                class="w-full border border-emerald-200 rounded-xl px-3 py-2 bg-white text-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200/50">
                                                <option value="">-- Seleccionar Conductor --</option>
                                                @foreach($drivers as $driver)
                                                    <option value="{{ $driver->id }}">{{ $driver->name }} (Licencia: {{ $driver->license }})</option>
                                                @endforeach
                                            </select>
                                            @error('selectedDriverId') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <button wire:click="generateShipment"
                                            class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition duration-150 shadow-sm whitespace-nowrap">
                                            Confirmar Despacho
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- Display assigned shipment detail -->
                            @if($selectedOrder->shipment)
                                <div class="mt-4 bg-indigo-50/50 border border-indigo-100 p-4 rounded-2xl">
                                    <h5 class="text-xs font-bold text-indigo-800 uppercase tracking-wider mb-1">Detalle del Envío</h5>
                                    <p class="text-sm text-gray-700">
                                        Asignado a: <span class="font-bold">{{ $selectedOrder->shipment->driver->name }}</span> (Tel: {{ $selectedOrder->shipment->driver->phone }})
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Estado de Envío: 
                                        <span class="font-semibold capitalize text-indigo-600">
                                            {{ $selectedOrder->shipment->status === 'in_transit' ? 'En tránsito' : ($selectedOrder->shipment->status === 'delivered' ? 'Entregado' : 'Fallido') }}
                                        </span>
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Items table -->
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Productos Comprados</h4>
                        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <tr>
                                        <th class="py-3 px-4">Producto</th>
                                        <th class="py-3 px-4 text-center">Cantidad</th>
                                        <th class="py-3 px-4 text-right">Precio Unitario</th>
                                        <th class="py-3 px-4 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-sm">
                                    @foreach($selectedOrder->items as $item)
                                        <tr>
                                            <td class="py-3.5 px-4">
                                                <div class="font-medium text-gray-800">{{ $item->name }}</div>
                                                @if($item->features && is_array($item->features))
                                                    <div class="text-xs text-gray-400 mt-0.5">
                                                        @foreach($item->features as $key => $feat)
                                                            <span class="mr-2">{{ $key }}: <strong class="text-gray-600">{{ $feat }}</strong></span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-4 text-center font-medium text-gray-600">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="py-3.5 px-4 text-right text-gray-600">
                                                ${{ number_format($item->price, 2) }}
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-semibold text-gray-800">
                                                ${{ number_format($item->price * $item->quantity, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Grand Total -->
                        <div class="flex justify-end mt-4">
                            <div class="w-full sm:w-64 text-right">
                                <div class="flex justify-between py-1 text-sm text-gray-500">
                                    <span>Subtotal:</span>
                                    <span>${{ number_format($selectedOrder->subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-1 text-sm text-gray-500">
                                    <span>Costo de Envío:</span>
                                    <span>${{ number_format($selectedOrder->shipping_cost, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-t border-gray-100 text-lg font-bold text-gray-800 mt-1">
                                    <span>Total:</span>
                                    <span class="text-indigo-700">${{ number_format($selectedOrder->total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end rounded-b-3xl">
                        <button wire:click="closeModal" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition duration-150 shadow-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
