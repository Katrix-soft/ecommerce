<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
    ['name' => 'Subcategorias', 'route' => route('admin.subcategories.index')],
    ['name' => 'Nuevo'],
]">
  
    @livewire('admin.subcategories.subcategory-create')

</x-admin-layout>
