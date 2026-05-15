<x-container>
     <div class="card">
        <div class="grid grid-cols-2 gap-6">
           <div class="col-span-1">
            <figure class="mb-2">
                 <img src="{{ $this->variant ? $this->variant->image : $product->image }}" class="aspect-[16/9] w-full object-cover object center" alt="{{ $product->name }}">
            </figure>
            <div class="text-sm text-gray-600">
                {{$product->description}}
            </div>
           </div>
           <div class="col-span-1">
            <h1 class="text-xl text-gray-600 mb-2">
                {{ $product->name }}
            </h1>

           <div class="flex items-center space-x-2 mb-4">
             <ul class="flex space-x-1 text-sm">
                <li>
                    <i class="fa-solid fa-star text-yellow-400"></i>
                </li>
                <li>
                    <i class="fa-solid fa-star text-yellow-400"></i>
                </li>
                <li>
                    <i class="fa-solid fa-star text-yellow-400"></i>
                </li>
                <li>
                    <i class="fa-solid fa-star text-yellow-400"></i>
                </li>
                <li>
                    <i class="fa-solid fa-star text-yellow-400"></i>
                </li>
            </ul>
            <p class="text-sm text-gray-600">
              4.7(55)  
            </p>
           </div>
           <p class="text-2xl text-gray-600 font-bold mb-4 text-gray-600">
            ${{ $product->price }}
           </p>

           <div class="mb-4">
               @foreach ($product->options as $option)
                   <div class="mb-6">
                       <p class="font-bold text-gray-700 mb-3 text-sm uppercase tracking-wide">{{ $option->name }}</p>
                       <ul class="flex flex-wrap items-center gap-3">
                           @foreach ($option->features->whereIn('id', $option->pivot->feature_id) as $feature)
                               <li>
                                   @if ($option->type == 1)
                                       {{-- Diseño para Texto (Talla, etc) --}}
                                       <button 
                                           wire:click="$set('selectedFeatures.{{ $option->id }}', {{ $feature->id }})"
                                           class="min-w-[50px] px-4 py-2 rounded-xl border-2 text-sm font-bold transition-all
                                           {{ $selectedFeatures[$option->id] == $feature->id 
                                               ? 'bg-purple-600 border-purple-600 text-white shadow-md' 
                                               : 'bg-white border-gray-100 text-gray-600 hover:border-purple-300' }}">
                                           {{ $feature->description }}
                                       </button>
                                   @elseif($option->type == 2)
                                       {{-- Diseño para Color --}}
                                       <button 
                                           wire:click="$set('selectedFeatures.{{ $option->id }}', {{ $feature->id }})"
                                           class="w-10 h-10 rounded-xl border-2 transition-all relative
                                           {{ $selectedFeatures[$option->id] == $feature->id 
                                               ? 'border-purple-600 ring-2 ring-purple-100' 
                                               : 'border-gray-100 hover:border-purple-300' }}"
                                           title="{{ $feature->description }}">
                                           <div class="absolute inset-1 rounded-lg" style="background-color: {{ $feature->value }}"></div>
                                       </button>
                                   @endif
                               </li>
                           @endforeach
                       </ul>
                   </div>
               @endforeach
           </div>

           <div class="flex items-center space-x-6 mb-6" x-data="{ quantity: @entangle('quantity') }">
            <button class="btn btn-gray cursor-pointer rounded-full hover:bg-red-500"
                x-on:click="if (quantity > 1) quantity--" x-bind:disabled="quantity <= 1"
                x-bind:class="{ 'opacity-50': quantity <= 1, 'hover:bg-red-500': quantity > 1 }">
                <i class="fa-solid fa-minus"></i>
            </button>
            <span x-text="quantity" class="btn btn-gray text-sm font-bold">
            </span>
            <button class="btn btn-gray cursor-pointer rounded-full hover:bg-green-500" x-on:click="quantity++" x-bind:class="{ 'opacity-50': quantity >= 10, 'hover:bg-green-500': quantity < 10 }">
                <i class="fa-solid fa-plus"></i>
            </button>
           </div>
           <button class="btn bg-purple-500 hover:bg-purple-600 text-white px-8 py-3 rounded-full w-full" wire:click="addItem"
           wire:loading.attr="disabled">
            Agregar al carrito
           </button>
           <div class="flex items-center space-x-4 mt-4 text-gray-600 text-sm">
            <i class="fa-solid fa-truck"></i>
            <p>Envio gratis</p>
           </div>
           </div>
        </div>
     </div>
   </x-container>