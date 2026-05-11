<div class="card p-6 mb-4 border border-gray-200 dark:border-gray-700 rounded-lg">
    <h3 class="text-lg font-semibold mb-4">Nueva opción</h3>

    <x-validation-errors class="mb-4" />

    <div class="mb-4">
        <x-label class="mb-2">Nombre</x-label>
        <x-input wire:model="name" class="w-full" placeholder="Ej: Talle, Color, Sexo" />
    </div>

    <div class="mb-4">
        <x-label class="mb-2">Tipo</x-label>
        <select wire:model="type"
            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
            <option value="1">Texto (lista desplegable)</option>
            <option value="2">Color (selector de color)</option>
        </select>
    </div>

    <div class="flex justify-end gap-2">
        <x-button type="button" wire:click="cancelCreate"
            class="bg-gray-500 hover:bg-gray-600">
            Cancelar
        </x-button>
        <x-button type="button" wire:click="save">
            Guardar
        </x-button>
    </div>
</div>
