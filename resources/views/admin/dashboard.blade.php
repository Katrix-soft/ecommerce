<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard'
    ],
]">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center ">
                <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                <div class="ml-4 flex-l">
                    <h2 class="text-lg font-bold text-gray-800">Bienvenido, {{ auth()->user()->name }}</h2>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class=" text-sm text-gray-500 hover:text-red-700">
                            Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 flex items-center justify-center">
            <h2 class="text-xl font-semibold">
              Tienda online    
            </h2>
        </div>

    </div>
</x-admin-layout>