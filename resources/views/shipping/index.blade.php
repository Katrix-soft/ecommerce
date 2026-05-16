<x-app-layout>
    <x-container class="py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                @livewire('shipping-addresses')
            </div>

            <div class="lg:col-span-1">
                {{-- Resumen de compra simplificado --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sticky top-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Resumen</h3>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600 text-sm">
                            <span>Subtotal</span>
                            <span class="font-bold">${{ Cart::instance('shopping')->subtotal() }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 text-sm">
                            <span>Envío</span>
                            <span class="text-green-500 font-bold italic">¡GRATIS!</span>
                        </div>
                        <div class="pt-4 border-t border-gray-50 flex justify-between">
                            <span class="font-bold text-gray-800">Total</span>
                            <span class="text-xl font-black text-purple-600">${{ Cart::instance('shopping')->total() }}</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-4 flex items-start gap-3">
                        <i class="fas fa-info-circle text-purple-500 mt-1"></i>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Selecciona una dirección de envío para continuar con el método de pago.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </x-container>
</x-app-layout>