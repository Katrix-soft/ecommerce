<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard',
    'route' => route('admin.dashboard'),
    ],
    ['name' => 'Familias']
]">

    <x-slot name="action">
        <a class="btn btn-blue" href="{{ route('admin.families.create') }}">
            Nuevo
        </a>
    </x-slot>

    @if ($families->count())
        
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
            <table class="w-full text-sm text-left rtl:text-right text-body">
                <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ( $families as $family )
                        <tr class="bg-neutral-primary border-b border-default">
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                {{ $family->id }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $family->name }}
                            </td>
                            <td class="px-6 py-4">
                            <a href="{{ route('admin.families.edit', $family) }}">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $families->links() }}
        </div>
    @else
    <div class="flex items-start sm:items-center p-4 mb-4 text-sm text-fg-brand-strong rounded-base bg-brand-softer" role="alert">
        <svg class="w-4 h-4 me-2 shrink-0 mt-0.5 sm:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
        <p><span class="font-medium me-1">Info alert!</span> Todavia no hay familias registradas.</p>
    </div>
   @endif
</x-admin-layout>