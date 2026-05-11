<div>
    <form wire:submit="save">
        <div class="card p-6">
            <x-validation-errors class="mb-4" />

            <!-- Familias -->
            <div class="mb-4">
                <x-label class="mb-2">Familias</x-label>
                <select wire:model.live="family_id" 
                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                    wire:key="family-select">
                    <option value="">Seleccione una familia</option>
                    @foreach ($families as $family)
                        <option value="{{ $family->id }}">{{ $family->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Categorias -->
            <div class="mb-4">
                <x-label class="mb-2">Categorias</x-label>
                <select wire:model.live="category_id" 
                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                    wire:key="category-select-{{ $family_id }}">
                    <option value="">Seleccione una categoria</option>
                    @foreach ($this->categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nombre -->
            <div class="mb-4">
                <x-label class="mb-2">Nombre</x-label>
                <x-input wire:model="name" class="w-full" placeholder="Ingrese el nombre de la subcategoria" />
            </div>

            <div class="flex justify-end">
                <x-danger-button type="button" onclick="confirmDelete()">
                    Eliminar
                </x-danger-button>
                <x-button class="ml-2">
                    Actualizar
                </x-button>
            </div>
        </div>
    </form>

    <form action="{{ route('admin.subcategories.destroy', $subcategory->id) }}" method="POST" id="delete-form">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete(){
            Swal.fire({
                title: "¿Estás seguro?",
                text: "No podrás revertir esto!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminar!",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        }
    </script>
</div>
