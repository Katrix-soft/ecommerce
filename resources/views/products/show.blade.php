<x-app-layout>
    <x-container class="px-4 my-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                    <a href="/" class="inline-flex items-center text-sm font-medium text-body hover:text-purple-600">
                        <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"/></svg>
                        Home
                    </a>
                    </li>

                    <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg>
                        <a href="{{ route('families.show', $product->subcategory->category->family) }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-purple-600 md:ms-2">
                            {{ $product->subcategory->category->family->name }}
                        </a>
                    </div>
                    </li>

                    <li>
                        <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg>
                        <a href="{{ route('categories.show', $product->subcategory->category) }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-purple-600 md:ms-2">
                            {{ $product->subcategory->name }}
                        </a>
                        </div>
                    </li>

                    <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg>
                        <a href="{{ route('subcategories.show', $product->subcategory) }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-purple-600 md:ms-2">
                            {{ $product->subcategory->name }}
                        </a>
                    </div>
                    </li>
                    
                </ol>
            </nav>

   </x-container>

   <x-container>
     <div class="card">
        <div class="grid grid-cols-2 gap-6">
           <div class="col-span-1">
            <figure class="mb-2">
                <img src="{{ $product->image}}" class="aspect-[16/9] w-full object-cover object center" alt="{{ $product->name }}">
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
           <div class="flex items-center space-x-6 mb-6">
            <button class="btn btn-gray cursor-pointer rounded-full hover:bg-red-500">
                <i class="fa-solid fa-minus"></i>
            </button>
            <span class="btn btn-gray text-sm font-bold">
                1
            </span>
            <button class="btn btn-gray cursor-pointer rounded-full hover:bg-green-500">
                <i class="fa-solid fa-plus"></i>
            </button>
           </div>
           <button class="btn bg-purple-500 hover:bg-purple-600 text-white px-8 py-3 rounded-full w-full">
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
</x-app-layout>