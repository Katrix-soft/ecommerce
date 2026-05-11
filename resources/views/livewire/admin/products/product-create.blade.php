<div class="card p-6">
    <x-validation-errors class="mb-4" />
    <form wire:submit=store>
    
    <figure class="mb-4 relative">

        <div class="absolute top-8 right-8">
            <label class="flex items-center px-4 py-2 rounded-lg bg-white text-gray-700 shadow-sm cursor-pointer border border-gray-200 hover:bg-gray-50 transition">
                <i class="fa-solid fa-camera mr-2"></i> Actualiza la imagen
                <input type="file" wire:model="image" accept="image/*" class="hidden" />
            </label>
        </div>
        <img class="aspect-[16/9] object-cover object-center w-full bg-neutral-primary-soft border border-default rounded-base overflow-hidden"
            src="{{ $image ? $image->temporaryUrl() : asset('img/no-image.png') }}" alt="">
    </figure>
    
    <div class="mb-4">
        <x-label class="mb-2">Código</x-label>
        <x-input type="text" wire:model="sku" class="w-full" placeholder="Ingrese el código del producto" />
    </div>

    <div class="mb-4">
        <x-label class="mb-2">Nombre</x-label>
        <x-input type="text" wire:model="name" class="w-full" placeholder="Ingrese el nombre del producto" />
    </div>

    <div class="mb-4">
        <x-label class="mb-2">Descripción</x-label>
        <x-textarea wire:model="description" class="w-full" placeholder="Ingrese la descripción del producto"></x-textarea>
    </div>

    <!-- Familias -->
    <div class="mb-4">
        <x-label class="mb-2">Familia</x-label>
        <select wire:model.live="family_id"
            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
            <option value="">Seleccione una familia</option>
            @foreach ($families as $family)
                <option value="{{ $family->id }}">{{ $family->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Categorias -->
    <div class="mb-4">
        <x-label class="mb-2">Categoría</x-label>
        <select wire:model.live="category_id"
            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            wire:key="category-select-{{ $family_id }}">
            <option value="">Seleccione una categoría</option>
            @foreach ($this->categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Subcategorias -->
    <div class="mb-4">
        <x-label class="mb-2">Subcategoría</x-label>
        <select wire:model.live="subcategory_id"
            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            wire:key="subcategory-select-{{ $category_id }}">
            <option value="">Seleccione una subcategoría</option>
            @foreach ($this->subcategories as $subcategory)
                <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <x-label class="mb-2">Precio</x-label>
        <x-input type="number" step="0.01" wire:model="price" class="w-full" placeholder="Ingrese el precio del producto" />
    </div>
    <div class="flex justify-end">
        <x-button type="submit">Guardar producto</x-button>
    </div>
</form>
</div>
