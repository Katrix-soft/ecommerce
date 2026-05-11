<div class="bg-white py-8" x-data="{ mobileFiltersOpen: false }">
    <x-container class="px-4">
        <div class="md:flex">
            <!-- Desktop Sidebar -->
            @if (count($options))
                <aside class="hidden md:block md:w-64 md:flex-shrink-0 md:mr-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Filtros</h2>
                        @if (count($selected_features))
                            <button wire:click="$set('selected_features', [])" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                Limpiar todo
                            </button>
                        @endif
                    </div>

                    <div class="space-y-6">
                        @foreach ($options as $option)
                            <div x-data="{ open: true }" wire:key="desktop-option-{{ $option['id'] }}" class="border-b border-gray-100 pb-6 last:border-0">
                                <button class="flex w-full justify-between items-center text-gray-800 font-semibold hover:text-indigo-600 transition-colors"
                                    x-on:click="open = !open">
                                    {{ $option['name'] }}
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="mt-4 space-y-3" x-show="open" x-collapse>
                                    @foreach ($option['features'] as $feature)
                                        <div wire:key="desktop-feature-{{ $feature['id'] }}">
                                            @if ($option['type'] == 2)
                                                <label class="flex items-center cursor-pointer group">
                                                    <input type="checkbox" value="{{ $feature['id'] }}"
                                                        wire:model.live="selected_features" class="sr-only peer">
                                                    <div class="w-7 h-7 rounded-full border border-gray-200 shadow-sm peer-checked:ring-2 peer-checked:ring-indigo-600 peer-checked:ring-offset-2 transition-all group-hover:scale-110"
                                                        style="background-color: {{ $feature['value'] }}"
                                                        title="{{ $feature['description'] }}">
                                                    </div>
                                                    <span class="ml-3 text-sm text-gray-600 group-hover:text-indigo-600 transition-colors">
                                                        {{ $feature['description'] }}
                                                    </span>
                                                </label>
                                            @else
                                                <label class="flex items-center cursor-pointer group">
                                                    <x-checkbox class="mr-3" value="{{ $feature['id'] }}"
                                                        wire:model.live="selected_features" />
                                                    <span class="text-sm text-gray-600 group-hover:text-indigo-600 transition-colors">
                                                        {{ $feature['description'] }}
                                                    </span>
                                                </label>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </aside>
            @endif

            <!-- Mobile/Tablet Filter Button -->
            <div class="md:hidden mb-6 flex items-center justify-between">
                <button @click="mobileFiltersOpen = true" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" /></svg>
                    Filtrar
                </button>
                
                @if (count($selected_features))
                    <button wire:click="$set('selected_features', [])" class="text-sm font-semibold text-indigo-600">
                        Limpiar ({{ count($selected_features) }})
                    </button>
                @endif
            </div>

            <!-- Products Content -->
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 space-y-4 sm:space-y-0">
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ count($products) }} Productos encontrados
                    </h1>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500 font-medium whitespace-nowrap">Ordenar por:</span>
                        <x-select class="text-sm">
                            <option value="1">Relevancia</option>
                            <option value="2">Precio: Mayor a menor</option>
                            <option value="3">Precio: Menor a mayor</option>
                        </x-select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-10">
                    @foreach($products as $product)
                        <article class="group relative flex flex-col bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="aspect-h-1 aspect-w-1 bg-gray-200 group-hover:opacity-75 sm:h-64 transition-opacity">
                                <img src="{{ $product->image }}" class="w-full h-full object-center object-cover">
                            </div>
                            <div class="flex-1 p-4 flex flex-col">
                                <h3 class="text-sm font-bold text-gray-900 line-clamp-2 min-h-[40px]">
                                    <a href="#">
                                        <span aria-hidden="true" class="absolute inset-0"></span>
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                <div class="mt-2 flex-1 flex flex-col justify-end">
                                    <p class="text-lg font-extrabold text-indigo-600">
                                        $ {{ number_format($product->price, 0, ',', '.') }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Stock disponible: {{ $product->variants->sum('stock') }}
                                    </p>
                                </div>
                                <button class="mt-4 w-full bg-indigo-600 border border-transparent rounded-md py-2 px-4 flex items-center justify-center text-sm font-semibold text-white hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Ver detalles
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </x-container>

    <!-- Mobile Filters Drawer -->
    <div x-show="mobileFiltersOpen" class="fixed inset-0 flex z-40 md:hidden" x-ref="dialog" role="dialog" aria-modal="true">
        <div x-show="mobileFiltersOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-25" aria-hidden="true"></div>

        <div x-show="mobileFiltersOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="ml-auto relative max-w-xs w-full h-full bg-white shadow-xl py-4 pb-12 flex flex-col overflow-y-auto">
            <div class="px-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Filtros</h2>
                <button type="button" @click="mobileFiltersOpen = false" class="-mr-2 w-10 h-10 bg-white p-2 rounded-md flex items-center justify-center text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="sr-only">Cerrar menú</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Filters -->
            <div class="mt-4 border-t border-gray-200">
                @foreach ($options as $option)
                    <div x-data="{ open: false }" class="border-t border-gray-200 px-4 py-6 first:border-0">
                        <h3 class="-mx-2 -my-3 flow-root">
                            <button type="button" @click="open = !open" class="px-2 py-3 bg-white w-full flex items-center justify-between text-gray-400 hover:text-gray-500" aria-controls="filter-section-0" aria-expanded="false">
                                <span class="font-bold text-gray-900">{{ $option['name'] }}</span>
                                <span class="ml-6 flex items-center">
                                    <svg class="h-5 w-5 transform transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </span>
                            </button>
                        </h3>
                        <div x-show="open" class="pt-6" id="filter-section-0">
                            <div class="space-y-6">
                                @foreach ($option['features'] as $feature)
                                    <div class="flex items-center">
                                        @if ($option['type'] == 2)
                                            <label class="flex items-center cursor-pointer group">
                                                <input type="checkbox" value="{{ $feature['id'] }}"
                                                    wire:model.live="selected_features" class="sr-only peer">
                                                <div class="w-7 h-7 rounded-full border border-gray-200 shadow-sm peer-checked:ring-2 peer-checked:ring-indigo-600 peer-checked:ring-offset-2 transition-all group-hover:scale-110"
                                                    style="background-color: {{ $feature['value'] }}"
                                                    title="{{ $feature['description'] }}">
                                                </div>
                                                <span class="ml-3 text-sm text-gray-600 group-hover:text-indigo-600 transition-colors">
                                                    {{ $feature['description'] }}
                                                </span>
                                            </label>
                                        @else
                                            <label class="flex items-center cursor-pointer group">
                                                <x-checkbox class="mr-3" value="{{ $feature['id'] }}"
                                                    wire:model.live="selected_features" />
                                                <span class="text-sm text-gray-600 group-hover:text-indigo-600 transition-colors">
                                                    {{ $feature['description'] }}
                                                </span>
                                            </label>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-auto px-4 pt-6 border-t border-gray-200">
                <button @click="mobileFiltersOpen = false" class="w-full bg-indigo-600 text-white py-3 rounded-md font-bold shadow-lg">
                    Aplicar Filtros
                </button>
            </div>
        </div>
    </div>
</div>
