<x-container class="py-12">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <aside class="md:col-span-1">
            <h2 class="font-bold text-lg mb-4">Filtros</h2>
            <div class="space-y-6">
                @foreach ($options as $option)
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">{{ $option->name }}</h3>
                        <ul class="space-y-1">
                            @foreach ($option->features as $feature)
                                <li>
                                    <label class="flex items-center">
                                        <x-checkbox />
                                        <span class="ml-2 text-gray-600 text-sm">{{ $feature->value }}</span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </aside>

        <div class="md:col-span-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <article class="bg-white shadow rounded overflow-hidden">
                        <img src="{{ $product->image }}" class="w-full h-48 object-cover object-center">

                        <div class="p-4">
                            <h1 class="text-lg font-bold text-gray-700 line-clamp-2 min-h-[56px] mb-2">
                                {{ $product->name }}
                            </h1>
                            <p class="text-gray-600 mb-4">
                                P/ {{ $product->price }}
                            </p>
                            <a href="" class="btn btn-blue block w-full text-center">
                                Ver más
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-container>

