<div class="bg-white py-12">
  <x-container class="px-4 md:flex">
        @if (count($options))
            <aside class="md:w-52 md:flex-shrink-0 md:mr-8 mb-8 md:mb-0">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-700">Filtros</h2>
                    @if (count($selected_features))
                        <button wire:click="$set('selected_features', [])" class="text-xs text-purple-600 hover:underline">
                            Limpiar
                        </button>
                    @endif
                </div>

                <ul class="space-y-4">
                    @foreach ($options as $option)
                        <li x-data="{ open: true }" wire:key="option-{{ $option['id'] }}" class="border-b border-gray-100 pb-4 last:border-0">
                            <button class="py-2 w-full flex justify-between items-center text-gray-700 font-medium hover:text-purple-600 transition-colors"
                                x-on:click="open = !open">
                                {{ $option['name'] }}
                                <i class="fa-solid text-xs transition-transform" :class="{ 'rotate-180': open, '': !open }">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </i>
                            </button>
                            <ul class="mt-2 space-y-3" x-show="open" x-collapse>
                                @foreach ($option['features'] as $feature)
                                    <li wire:key="feature-{{ $feature['id'] }}">
                                        @if ($option['type'] == 2)
                                            <label class="relative flex items-center cursor-pointer group">
                                                <input type="checkbox" value="{{ $feature['id'] }}"
                                                    wire:model.live="selected_features" class="sr-only peer">

                                                <div class="w-6 h-6 rounded-full border border-gray-200 shadow-sm peer-checked:ring-2 peer-checked:ring-purple-500 peer-checked:ring-offset-2 transition-all group-hover:scale-110"
                                                    style="background-color: {{ $feature['value'] }}"
                                                    title="{{ $feature['description'] }}">
                                                </div>

                                                <span class="ml-3 text-sm text-gray-600 group-hover:text-purple-600 transition-colors">
                                                    {{ $feature['description'] }}
                                                </span>
                                            </label>
                                        @else
                                            <label class="inline-flex items-center cursor-pointer group">
                                                <x-checkbox class="mr-2 !text-purple-600 !focus:ring-purple-500" value="{{ $feature['id'] }}"
                                                    wire:model.live="selected_features" />
                                                <span class="text-sm text-gray-600 group-hover:text-purple-600 transition-colors">
                                                    {{ $feature['description'] }}
                                                </span>
                                            </label>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>

                        </li>
                    @endforeach
                </ul>
            </aside>
        @endif
       <div class="md:flex-1">
              <div class="flex items-center">
                 <span class="mr-2">
                    Ordenar por:
                 </span>
                 <x-select>
                    <option value="1">Relevancia</option>
                    <option value="2">Precio: Mayor a menor</option>
                    <option value="3">Precio: Menor a mayor</option>
                 </x-select>
              </div>

             <hr class="my-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                 @foreach($products as $product)
                 <article class="bg-white shadow rounded overflow-hidden">
                    <img src="{{ $product->image }}" class="w-full h-48 object-cover object-center">

                    <div class="p-4">
                      <h1 class="text-lg font-bold text-gray-700 line-clamp-2 min-h-[56px] mb-2">
                         {{ $product->name }}
                      </h1>
                      <p class="text-gray-600 mb-4">
                         $ {{ $product->price }}
                      </p>
                      <a href="" class="btn btn-purple block w-full text-center">
                         Ver más
                      </a>
       
                    </div>
        
      
                 </article>
                 @endforeach
                    
                 

                </div>
                <div class="mt-8">
                    {{  $products->links() }}
                 </div>

                 
        </div>
  </x-container>
</div>
