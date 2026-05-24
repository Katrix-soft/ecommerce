<div class="bg-gray-50 rounded-2xl border border-gray-200 px-4 py-3 mb-4 mt-3 shadow-sm">
    <h3 class="text-sm font-bold text-gray-800 mb-3">Resumen de tu compra</h3>
    
    <!-- List items in cart -->
    <div class="max-h-36 overflow-y-auto mb-3 pr-1 space-y-2 custom-scrollbar">
        @foreach (Cart::instance('shopping')->content() as $item)
            @php
                $isExceeded = $item->qty > ($stocks[$item->id] ?? 0);
            @endphp
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 {{ $isExceeded ? 'bg-red-50 text-red-500 border-red-100' : 'bg-white text-purple-600 border-gray-100/50' }} rounded-lg flex items-center justify-center font-bold text-[10px] border shadow-sm shrink-0">
                    {{ $item->qty }}x
                </div>
                <div class="flex-1 min-w-0">
                    @if ($isExceeded)
                        <span class="text-[8px] text-red-500 font-bold block">Stock insuficiente</span>
                    @endif
                    <p class="text-[11px] font-bold {{ $isExceeded ? 'text-red-500' : 'text-gray-800' }} truncate" title="{{ $item->name }}">{{ $item->name }}</p>
                </div>
                <span class="text-[11px] font-black {{ $isExceeded ? 'text-red-400 line-through' : 'text-gray-800' }} shrink-0">{{ \App\Models\User::formatPrice($item->price * $item->qty) }}</span>
            </div>
        @endforeach
    </div>

    <!-- Price Breakdown -->
    <div class="space-y-1.5 pt-3 border-t border-gray-200 mb-3">
        <div class="flex justify-between text-gray-500 text-[11px] font-medium">
            <span>Subtotal</span>
            <span class="font-bold text-gray-800">{{ \App\Models\User::formatPrice($subtotal) }}</span>
        </div>
        <div class="flex justify-between text-gray-500 text-[11px] font-medium">
            <span>Envío</span>
            <span class="text-green-600 font-extrabold italic">¡GRATIS!</span>
        </div>
        <div class="pt-2 border-t border-gray-200 flex justify-between items-center">
            <span class="font-black text-gray-800 text-xs">Total</span>
            <span class="text-base font-black text-purple-600">{{ \App\Models\User::formatPrice($total) }}</span>
        </div>
    </div>

    @if ($hasStockErrors)
        <div class="p-2.5 bg-amber-50 border border-amber-100 rounded-xl text-center mb-3">
            <p class="text-[9px] text-amber-600 font-black uppercase tracking-wider mb-1">Ajuste de Stock</p>
            <p class="text-[9px] text-gray-500 leading-normal mb-2">Los productos marcados con stock insuficiente no se incluirán ni cobrarán.</p>
            <a href="{{ route('cart.index') }}" class="inline-block w-full py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-[9px] font-bold rounded-lg transition-all shadow-sm">
                Modificar carrito
            </a>
        </div>
    @endif

    <!-- Sidebar Tips -->
    <div class="bg-white rounded-xl p-2.5 flex items-center gap-2 border border-gray-100 shadow-sm">
        <i class="fas fa-shield-alt text-purple-500 text-[10px]"></i>
        <p class="text-[9px] text-gray-500 leading-snug">
            Compra 100% protegida. Cumplimos con los estándares de seguridad de datos de la industria bancaria.
        </p>
    </div>
</div>
