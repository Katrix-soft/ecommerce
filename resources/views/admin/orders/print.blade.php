<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Despacho #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700&display=swap" rel="stylesheet" />
    <!-- Tailwind CSS (compiled via cdn for absolute print styling) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                margin: 0.5cm;
                size: portrait;
            }
            .no-print {
                display: none !important;
            }
            body {
                background-color: white !important;
                color: black !important;
                font-size: 10px !important;
                padding: 0 !important;
            }
            .mb-6 { margin-bottom: 10px !important; }
            .mb-5 { margin-bottom: 10px !important; }
            .pb-5 { padding-bottom: 10px !important; }
            .mb-10 { margin-bottom: 10px !important; }
            .mt-10 { margin-top: 15px !important; }
            .pt-10 { padding-top: 15px !important; }
            .py-3 { padding-top: 4px !important; padding-bottom: 4px !important; }
            h1 { font-size: 16px !important; }
            h3 { font-size: 11px !important; margin-bottom: 2px !important; }
            p, span, td, th { font-size: 10px !important; }
            /* Hide URL printing from browsers */
            a[href]:after {
                content: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800 p-4 sm:p-8">

    <!-- Action Bar (hidden on print) -->
    <div class="max-w-2xl mx-auto mb-6 bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between no-print">
        <span class="text-sm font-medium text-gray-600">Vista de impresión de Ticket de Despacho.</span>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition duration-150">
                Imprimir / PDF
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-lg transition duration-150">
                Cerrar Ventana
            </button>
        </div>
    </div>

    <!-- Ticket Container -->
    <div class="max-w-2xl mx-auto bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-lg print:border-0 print:shadow-none print:p-0">
        <!-- Logo and Title -->
        <div class="flex justify-between items-start border-b border-gray-100 pb-5 mb-5">
            <div>
                <h1 class="text-xl font-bold tracking-wider text-indigo-900 uppercase">SHOPLY E-COMMERCE</h1>
                <p class="text-xs text-gray-400 mt-0.5">Moda, Hogar y Tecnología a tu puerta</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold rounded-lg text-xs tracking-wider uppercase">
                    TICKET DE DESPACHO
                </span>
                <p class="text-sm font-semibold text-gray-700 mt-2">Nº: #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Fecha: {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- Meta info columns -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Destinatario</h3>
                <p class="font-bold text-gray-800 text-sm">{{ $order->user->name }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Email: {{ $order->user->email }}</p>
                @if($order->user->dni)
                    <p class="text-xs text-gray-500">Documento: {{ $order->user->document_type ?? 'DNI' }} {{ $order->user->dni }}</p>
                @endif
            </div>
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Información del Envío</h3>
                @php
                    $addr = $order->shipping_address;
                @endphp
                @if(is_array($addr))
                    <p class="font-semibold text-gray-800 text-sm">Recibe: {{ $addr['contact'] ?? 'Contacto' }}</p>
                    <p class="text-xs text-gray-500">Tel: {{ $addr['phone'] ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-600 font-semibold mt-1">{{ $addr['address'] }} {{ $addr['apartment'] ? ', '.$addr['apartment'] : '' }}</p>
                    <p class="text-xs text-gray-500">{{ $addr['locality'] }}, {{ $addr['province'] }} (CP: {{ $addr['zip_code'] }})</p>
                    @if(!empty($addr['reference']))
                        <p class="text-xs text-indigo-700 font-semibold italic mt-1">Ref: {{ $addr['reference'] }}</p>
                    @endif
                @else
                    <p class="text-xs text-gray-600">{{ $order->shipping_address }}</p>
                @endif
            </div>
        </div>

        <!-- Order details -->
        <div class="grid grid-cols-3 gap-4 border-t border-b border-gray-100 py-3 mb-6">
            <div>
                <span class="text-xs font-bold text-gray-400 block uppercase">Método de Pago</span>
                <span class="text-xs font-semibold text-gray-700 capitalize">
                    @if($order->payment_method === 'credit_card')
                        Tarjeta de Crédito/Débito
                    @elseif($order->payment_method === 'bank_transfer')
                        Transferencia Bancaria
                    @else
                        Efectivo / Contraentrega
                    @endif
                </span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 block uppercase">Estado de Pago</span>
                <span class="text-xs font-semibold text-gray-700 capitalize">
                    {{ $order->payment_status === 'paid' ? 'Pagado' : 'Pendiente' }}
                </span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 block uppercase">Conductor Asignado</span>
                <span class="text-xs font-semibold text-gray-700">
                    {{ $order->shipment && $order->shipment->driver ? $order->shipment->driver->name : 'No Asignado' }}
                </span>
            </div>
        </div>

        <!-- Purchased items -->
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Detalle de Productos</h3>
        <table class="w-full text-left mb-6">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase">
                    <th class="py-2 px-3">Producto</th>
                    <th class="py-2 px-3 text-center w-20">Cant.</th>
                    <th class="py-2 px-3 text-right w-28">P. Unitario</th>
                    <th class="py-2 px-3 text-right w-28">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs">
                @foreach($order->items as $item)
                    <tr>
                        <td class="py-3 px-3">
                            <span class="font-bold text-gray-800">{{ $item->name }}</span>
                            @if($item->features && is_array($item->features))
                                <div class="text-[10px] text-gray-400 mt-0.5">
                                    @foreach($item->features as $key => $feat)
                                        <span class="mr-2">{{ $key }}: <strong>{{ $feat }}</strong></span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center font-semibold text-gray-700">{{ $item->quantity }}</td>
                        <td class="py-3 px-3 text-right text-gray-600">${{ number_format($item->price, 2) }}</td>
                        <td class="py-3 px-3 text-right font-bold text-gray-800">${{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals block -->
        <div class="flex justify-end border-t border-gray-100 pt-4 mb-10">
            <div class="w-64 text-right text-xs">
                <div class="flex justify-between py-1 text-gray-500">
                    <span>Subtotal:</span>
                    <span>${{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between py-1 text-gray-500">
                    <span>Costo de Envío:</span>
                    <span>${{ number_format($order->shipping_cost, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-t border-gray-200 text-sm font-bold text-gray-800 mt-1">
                    <span>Total:</span>
                    <span class="text-indigo-800">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Receipt signature lines for driver/client -->
        <div class="grid grid-cols-2 gap-8 border-t border-dashed border-gray-200 pt-10 mt-10">
            <div class="text-center">
                <div class="border-b border-gray-300 w-44 mx-auto h-12"></div>
                <p class="text-xs font-semibold text-gray-500 mt-2">Firma del Conductor</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Despachado conforme</p>
            </div>
            <div class="text-center">
                <div class="border-b border-gray-300 w-44 mx-auto h-12"></div>
                <p class="text-xs font-semibold text-gray-500 mt-2">Firma del Cliente</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Recibido conforme</p>
            </div>
        </div>
    </div>

    <!-- Automatically trigger browser print dialog -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
