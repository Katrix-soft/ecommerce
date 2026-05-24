<div class="bg-gray-50 rounded-3xl border border-gray-200 p-6 mb-8 mt-4 shadow-sm">
    <h3 class="text-lg font-bold text-gray-800 mb-6">Resumen de tu compra</h3>
    
    <!-- List items in cart -->
    <div class="max-h-56 overflow-y-auto mb-6 pr-2 space-y-4 custom-scrollbar">
        @foreach (Cart::instance('shopping')->content() as $item)
            @php
                $isExceeded = $item->qty > ($stocks[$item->id] ?? 0);
            @endphp
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 {{ $isExceeded ? 'bg-red-50 text-red-500 border-red-100' : 'bg-white text-purple-600 border-gray-100/50' }} rounded-xl flex items-center justify-center font-bold text-xs border shadow-sm">
                    {{ $item->qty }}x
                </div>
                <div class="flex-1 min-w-0">
                    @if ($isExceeded)
                        <span class="text-[9px] text-red-500 font-bold block mb-0.5">Stock insuficiente</span>
                    @endif
                    <p class="text-xs font-bold {{ $isExceeded ? 'text-red-500' : 'text-gray-800' }} truncate" title="{{ $item->name }}">{{ $item->name }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ \App\Models\User::formatPrice($item->price) }} c/u</p>
                </div>
                <span class="text-xs font-black {{ $isExceeded ? 'text-red-400 line-through' : 'text-gray-800' }}">{{ \App\Models\User::formatPrice($item->price * $item->qty) }}</span>
            </div>
        @endforeach
    </div>

    <!-- Price Breakdown -->
    <div class="space-y-3 pt-6 border-t border-gray-200 mb-6">
        <div class="flex justify-between text-gray-500 text-xs font-medium">
            <span>Subtotal</span>
            <span class="font-bold text-gray-800">{{ \App\Models\User::formatPrice($subtotal) }}</span>
        </div>
        <div class="flex justify-between text-gray-500 text-xs font-medium">
            <span>Envío</span>
            <span class="text-green-600 font-extrabold italic">¡GRATIS!</span>
        </div>
        <div class="pt-4 border-t border-gray-200 flex justify-between items-center">
            <span class="font-black text-gray-800 text-sm">Total</span>
            <span class="text-xl font-black text-purple-600">{{ \App\Models\User::formatPrice($total) }}</span>
        </div>
    </div>

    @if ($hasStockErrors)
        <div class="mt-4 p-4 bg-amber-50 border border-amber-100 rounded-2xl text-center mb-6">
            <p class="text-[10px] text-amber-600 font-black uppercase tracking-wider mb-2">Ajuste de Stock</p>
            <p class="text-[10px] text-gray-500 leading-normal mb-3">Los productos marcados con stock insuficiente no se incluirán ni cobrarán.</p>
            <a href="{{ route('cart.index') }}" class="inline-block w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-bold rounded-xl transition-all shadow-sm">
                Modificar carrito
            </a>
        </div>
    @endif

    <!-- Sidebar Tips -->
    <div class="bg-white rounded-2xl p-4 flex items-start gap-3 border border-gray-100 shadow-sm">
        <i class="fas fa-shield-alt text-purple-500 mt-0.5 text-sm"></i>
        <p class="text-[10px] text-gray-500 leading-relaxed">
            Compra 100% protegida. Cumplimos con los estándares de seguridad de datos de la industria bancaria.
        </p>
    </div>
</div>
