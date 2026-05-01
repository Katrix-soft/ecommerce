@php
    $links = [
        [
            'icon' => 'fa-solid fa-gauge',
            'label' => 'Dashboard',
            'route' => route('admin.dashboard'),
            'active' => request()->routeIs('admin.dashboard'),
        ],
        [
            'icon' => 'fa-solid fa-box-open',
            'label' => 'Familias',
            'route' => route('admin.families.index'),
            'active' => request()->routeIs('admin.families.*'),
        ],
        [
            'icon' => 'fa-solid fa-tags',
            'label' => 'Categorias',
            'route' => route('admin.categories.index'),
            'active' => request()->routeIs('admin.categories.*'),
        ],
        [
            'icon' => 'fa-solid fa-tag',
            'label' => 'Subcategorias',
            'route' => route('admin.subcategories.index'),
            'active' => request()->routeIs('admin.subcategories.*'),
        ],
    ];
@endphp

<aside id="top-bar-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-[100dvh] transition-transform -translate-x-full sm:translate-x-0"
    :class="{
        'translate-x-0 ease-in-out duration-300': sidebarOpen,
        '-translate-x-full ease-in-out duration-300': !sidebarOpen
    }"aria-label="Sidebar">
    <div class="h-full px-3 py-4 overflow-y-auto bg-white border-e border-gray-200 dark:border-gray-700 pt-20">
        <ul class="space-y-2 font-medium">
            @foreach ($links as $link)
                <li>
                    <a href="{{ $link['route'] }}"
                        class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group {{ $link['active'] ? 'bg-gray-100' : '' }}">
                        <span class="inline-flex w-6 h-6 justify-center items-center">
                            <i class="{{ $link['icon'] }} text-gray-500"></i>
                        </span>

                        <span class="ml-2">
                            {{ $link['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</aside>
