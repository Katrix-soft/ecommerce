<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard',
    'route' => route('admin.dashboard'),
    ],
    ['name' => 'Productos']
]">

    <x-slot name="action">
        <div class="flex items-center space-x-2">
            @if(auth()->user()->hasModule('ai_import'))
                <button class="btn btn-red bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="document.getElementById('archivoInput').click()">
                    Cargar documento
                </button>
                <input type="file" id="archivoInput" style="display: none;" accept=".txt,.json,.csv" />
            @endif
            <a class="btn btn-blue" href="{{ route('admin.products.create') }}">
                Nuevo
            </a>
        </div>
    </x-slot>
    <div class="mb-4">
        <form action="{{ route('admin.products.index') }}" method="GET" class="w-full max-w-md">
            <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Buscar</label>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="search" name="search" value="{{ request('search') }}" id="default-search" class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar por Nombre o SKU..." oninput="if(this.value === '') this.form.submit()">
                <button type="submit" class="text-white absolute end-1.5 bottom-1.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Buscar</button>
            </div>
        </form>
    </div>

    <div id="resultadoIA" class="mb-4 text-sm text-gray-700 dark:text-gray-300 font-semibold"></div>

    @if ($products->count())
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3">
                            SKU
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nombre
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Precio
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <span class="sr-only">Editar</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->id }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $product->sku }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $product->name }}
                            </td>
                            <td class="px-6 py-4">
                                ${{ number_format($product->price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @else
        <div class="flex items-center p-4 mb-4 text-sm text-blue-800 border border-blue-300 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400 dark:border-blue-800"
            role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <span class="font-medium">Info!</span> Todavía no hay productos registrados.
            </div>
        </div>
    @endif

    @push('js')
    <script>
        const searchInput = document.getElementById('default-search');
        
        // --- AUTOCOMPLETE BÚSQUEDA ---
        if (searchInput) {
            const autocompleteList = document.createElement('div');
            autocompleteList.setAttribute('id', 'autocomplete-list');
            autocompleteList.className = 'absolute z-10 w-full bg-white rounded-lg shadow-lg dark:bg-gray-700 mt-1 hidden';
            searchInput.parentNode.appendChild(autocompleteList);

            let timeout = null;

            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                const val = this.value;
                
                if (!val) {
                    autocompleteList.classList.add('hidden');
                    this.form.submit();
                    return;
                }

                timeout = setTimeout(async () => {
                    try {
                        const response = await fetch(`{{ route('admin.products.suggestions') }}?q=${encodeURIComponent(val)}`);
                        const data = await response.json();

                        autocompleteList.innerHTML = '';
                        
                        if (data.length > 0) {
                            autocompleteList.classList.remove('hidden');
                            data.forEach(item => {
                                const div = document.createElement('div');
                                div.className = 'p-2.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-sm text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 last:border-0';
                                div.innerHTML = `<strong>${item.sku}</strong> - ${item.name}`;
                                div.addEventListener('click', function() {
                                    searchInput.value = item.name;
                                    autocompleteList.classList.add('hidden');
                                    searchInput.form.submit();
                                });
                                autocompleteList.appendChild(div);
                            });
                        } else {
                            autocompleteList.classList.add('hidden');
                        }
                    } catch (e) {
                        console.error('Error fetching suggestions', e);
                    }
                }, 300);
            });

            document.addEventListener('click', function (e) {
                if (e.target !== searchInput) {
                    autocompleteList.classList.add('hidden');
                }
            });
        }

        // --- SUBIDA DE DOCUMENTOS ---
        const btnCargar = document.querySelector('.btn-red');
        const archivoInput = document.getElementById('archivoInput');
        const resultadoDiv = document.getElementById('resultadoIA');

        // Detectar selección de archivo y subir automáticamente
        if (archivoInput) {
            archivoInput.addEventListener('change', async (event) => {
                const file = event.target.files[0];
                if (!file) return;

            resultadoDiv.innerText = "Subiendo y analizando con la IA... por favor espera.";

            // Preparamos los datos para enviar al controlador de Laravel
            const formData = new FormData();
            formData.append('documento', file);

            try {
                // Hacemos la petición a la ruta segura bajo el namespace admin
                const response = await fetch('{{ route("admin.products.parse_document") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Si existe Swal cargado en el layout, mostramos la alerta y luego recargamos
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Importación exitosa!',
                            text: data.message || 'Se han guardado los productos.',
                            confirmButtonColor: '#3085d6',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    } else {
                        alert(data.message || 'Se han guardado los productos.');
                        window.location.reload();
                    }
                } else {
                    resultadoDiv.innerText = "Error: " + data.message;
                }

            } catch (error) {
                console.error("Error en la petición:", error);
                resultadoDiv.innerText = "Hubo un error de conexión con el servidor.";
            } finally {
                // Limpiamos el input por si quiere subir el mismo archivo otra vez
                archivoInput.value = '';
            }
        });
    }
    </script>
    @endpush
</x-admin-layout>