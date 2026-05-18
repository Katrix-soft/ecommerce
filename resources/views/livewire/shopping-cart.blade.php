<x-container class="py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Listado de productos --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">Tu Carrito</h2>
                    <button wire:click="clearCart" class="text-sm text-red-500 hover:text-red-600 font-medium transition-colors">
                        Vaciar carrito
                    </button>
                </div>

                <div class="divide-y divide-gray-50">
                    @forelse ($cart as $item)
                        <div class="p-6 flex flex-col sm:flex-row items-center gap-6 group transition-all hover:bg-gray-50/50">
                            {{-- Imagen --}}
                            <div class="w-32 h-32 flex-shrink-0 rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
                                <img src="{{ $item->options->image }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                            </div>

                            {{-- Info --}}
                            <div class="flex-grow text-center sm:text-left">
                                @if ($item->qty > ($stocks[$item->id] ?? 0))
                                    <span class="text-xs text-red-500 font-bold block mb-1">
                                        Stock insuficiente
                                    </span>
                                @endif
                                <h3 class="text-lg font-bold {{ $item->qty > ($stocks[$item->id] ?? 0) ? 'text-red-500' : 'text-gray-800' }} mb-1">{{ $item->name }}</h3>
                                <div class="flex flex-wrap justify-center sm:justify-start gap-2 mb-3">
                                    @foreach ($item->options->features as $option => $feature)
                                        <span class="px-2.5 py-0.5 rounded-lg bg-purple-50 text-purple-600 text-[10px] font-bold uppercase tracking-wider">
                                            {{ $option }}: {{ $feature }}
                                        </span>
                                    @endforeach
                                </div>

                                <div class="text-xl font-black text-purple-600">${{ number_format($item->price, 2) }}</div>
                            </div>

                            {{-- Cantidad --}}
                            <div class="flex items-center gap-4">
                                <div class="flex items-center bg-gray-100 rounded-xl p-1">
                                    <button wire:click="decreaseQty('{{ $item->rowId }}')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white hover:shadow-sm transition-all text-gray-600">
                                        <i class="fa-solid fa-minus text-xs"></i>
                                    </button>
                                    <span class="w-10 text-center font-bold {{ $item->qty > ($stocks[$item->id] ?? 0) ? 'text-red-600 font-extrabold scale-110' : 'text-gray-800' }}">{{ $item->qty }}</span>
                                    <button wire:click="increaseQty('{{ $item->rowId }}')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white hover:shadow-sm transition-all text-gray-600">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                </div>
                                
                                <button wire:click="removeItem('{{ $item->rowId }}')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-400 hover:bg-red-500 hover:text-white transition-all duration-300">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-20 text-center">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fa-solid fa-cart-shopping text-4xl text-gray-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Tu carrito está vacío</h3>
                            <p class="text-gray-500 mb-8">Parece que aún no has agregado nada a tu carrito.</p>
                            <a href="/" class="inline-flex items-center px-8 py-3 bg-purple-600 text-white font-bold rounded-2xl hover:bg-purple-700 transition-all shadow-lg shadow-purple-200">
                                Empezar a comprar
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Resumen --}}
        @if ($cart->count() > 0)
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 p-6 sm:p-8 sticky top-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-8">Resumen de compra</h3>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-bold">${{ $subtotal }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Envío</span>
                            <span class="text-green-500 font-bold italic text-sm">¡GRATIS!</span>
                        </div>
                        <div class="pt-4 border-t border-gray-50 flex justify-between">
                            <span class="text-xl font-bold text-gray-800">Total</span>
                            <span class="text-2xl font-black text-purple-600">${{ $total }}</span>
                        </div>
                    </div>

                    <button wire:click="checkout" 
                        {{ !$hasValidItems ? 'disabled' : '' }}
                        @style(['opacity: 0.5; cursor: not-allowed; pointer-events: none;' => !$hasValidItems])
                        class="w-full py-4 bg-purple-600 text-white font-bold rounded-2xl hover:bg-purple-700 transition-all shadow-lg shadow-purple-200 flex items-center justify-center gap-3 active:scale-95">
                        <span>Continuar con el pago</span>
                        <i class="fa-solid fa-arrow-right text-sm"></i>
                    </button>

                    @if (!$hasValidItems)
                        <p class="text-[10px] text-red-500 font-black text-center mt-3 tracking-wider uppercase">
                            No tienes productos con stock para continuar.
                        </p>
                    @elseif ($hasStockErrors)
                        <p class="text-[10px] text-amber-600 font-bold text-center mt-3 tracking-wider uppercase">
                            Los productos sin stock no se incluirán en el pago.
                        </p>
                    @endif

                    <div class="mt-6 flex items-center justify-center gap-4 text-gray-400">
                        <i class="fa-brands fa-cc-visa text-2xl"></i>
                        <i class="fa-brands fa-cc-mastercard text-2xl"></i>
                        <i class="fa-brands fa-cc-paypal text-2xl"></i>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-container>
