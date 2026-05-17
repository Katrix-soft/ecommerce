<div>
    <!-- Header with premium gradient border -->
    <div class="mb-6 bg-gradient-to-r from-purple-500/10 via-indigo-500/10 to-blue-500/10 p-6 rounded-2xl border border-indigo-100 shadow-sm backdrop-blur-md">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-700 to-indigo-800 bg-clip-text text-transparent">Monitoreo de Envíos</h1>
        <p class="text-sm text-gray-500 mt-1">Supervisa en tiempo real el estado de entrega de todos los pedidos y sus respectivos conductores.</p>
    </div>

    <!-- Quick Stats Cards with micro-animations -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">En Tránsito</p>
                    <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ \App\Models\Shipment::where('status', 'in_transit')->count() }}</h3>
                </div>
                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                    <i class="fa-solid fa-truck-fast text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Entregados</p>
                    <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ \App\Models\Shipment::where('status', 'delivered')->count() }}</h3>
                </div>
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Fallidos</p>
                    <h3 class="text-2xl font-bold text-rose-600 mt-1">{{ \App\Models\Shipment::where('status', 'failed')->count() }}</h3>
                </div>
                <div class="p-3 bg-rose-50 rounded-xl text-rose-600">
                    <i class="fa-solid fa-circle-xmark text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter controls -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm mb-6 flex flex-col md:flex-row gap-4">
        <!-- Status Filter -->
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Filtrar por Estado</label>
            <select wire:model.live="statusFilter" 
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 bg-white text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200/50 transition">
                <option value="">Todos los Estados</option>
                <option value="in_transit">En Tránsito</option>
                <option value="delivered">Entregados</option>
                <option value="failed">Fallidos</option>
            </select>
        </div>
        
        <!-- Driver Filter -->
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Filtrar por Conductor</label>
            <select wire:model.live="driverFilter" 
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 bg-white text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200/50 transition">
                <option value="">Todos los Conductores</option>
                @foreach ($drivers as $driver)
                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Shipments Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Envío ID</th>
                        <th class="py-4 px-6">Pedido</th>
                        <th class="py-4 px-6">Conductor</th>
                        <th class="py-4 px-6">Destinatario</th>
                        <th class="py-4 px-6">Dirección de Reparto</th>
                        <th class="py-4 px-6 text-center">Estado Envío</th>
                        <th class="py-4 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse ($shipments as $shipment)
                        <tr class="hover:bg-indigo-50/20 transition-all duration-150">
                            <td class="py-4 px-6 font-mono font-semibold text-indigo-700">
                                #ENV-{{ str_pad($shipment->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-4 px-6 font-semibold">
                                Pedido #{{ str_pad($shipment->order_id, 6, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-800">{{ $shipment->driver->name }}</div>
                                <div class="text-xs text-gray-400">Licencia: {{ $shipment->driver->license }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-semibold text-gray-900">{{ $shipment->order->user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $shipment->order->user->email }}</div>
                            </td>
                            <td class="py-4 px-6 text-gray-600">
                                @php
                                    $addr = $shipment->order->shipping_address;
                                @endphp
                                @if(is_array($addr))
                                    <div class="font-medium">{{ $addr['address'] }} {{ $addr['apartment'] ? ', '.$addr['apartment'] : '' }}</div>
                                    <div class="text-xs text-gray-400">{{ $addr['locality'] }}, {{ $addr['province'] }}</div>
                                @else
                                    {{ $shipment->order->shipping_address }}
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @php
                                    $shipmentBadges = [
                                        'in_transit' => 'bg-indigo-50 text-indigo-700 border-indigo-100 animate-pulse',
                                        'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'failed' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    ];
                                    $shipmentTexts = [
                                        'in_transit' => 'En Tránsito',
                                        'delivered' => 'Entregado',
                                        'failed' => 'Fallido',
                                    ];
                                    $sBadge = $shipmentBadges[$shipment->status] ?? 'bg-gray-50 text-gray-700';
                                    $sText = $shipmentTexts[$shipment->status] ?? $shipment->status;
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $sBadge }}">
                                    {{ $sText }}
                                </span>
                                @if($shipment->delivered_at && $shipment->status === 'delivered')
                                    <div class="text-[10px] text-gray-400 mt-1">El {{ $shipment->delivered_at->format('d/m H:i') }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if ($shipment->status === 'in_transit')
                                        <button wire:click="markAsDelivered({{ $shipment->id }})" title="Marcar como Entregado"
                                            class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-xl transition duration-150">
                                            <i class="fa-solid fa-circle-check text-base"></i>
                                        </button>
                                        <button wire:click="markAsFailed({{ $shipment->id }})" title="Marcar como Fallido"
                                            class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition duration-150">
                                            <i class="fa-solid fa-circle-xmark text-base"></i>
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Sin Acciones</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-truck-ramp-box text-3xl mb-2 text-gray-300"></i>
                                    <span>No se encontraron envíos.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50">
            {{ $shipments->links() }}
        </div>
    </div>
</div>
