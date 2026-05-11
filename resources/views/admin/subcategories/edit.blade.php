<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
    ['name' => 'Subcategorias', 'route' => route('admin.subcategories.index')],
    ['name' => $subcategory->name],
]">
    <div class="card">
        @livewire('admin.subcategories.subcategory-edit', ['subcategory' => $subcategory])
    </div>
</x-admin-layout>
