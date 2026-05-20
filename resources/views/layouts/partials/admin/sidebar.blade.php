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
        [
            'icon' => 'fa-solid fa-box',
            'label' => 'Productos',
            'route' => route('admin.products.index'),
            'active' => request()->routeIs('admin.products.*'),
        ],
        [
            'icon' => 'fa-solid fa-cog',
            'label' => 'Opciones',
            'route' => route('admin.options.index'),
            'active' => request()->routeIs('admin.options.*'),
        ],
        [
            'label' => 'Portadas',
            'icon' => 'fa-solid fa-image',
            'route' => route('admin.covers.index'),
            'active' => request()->routeIs('admin.covers.*'),
        ],
        [
            'label' => 'Órdenes',
            'icon' => 'fa-solid fa-receipt',
            'route' => route('admin.orders.index'),
            'active' => request()->routeIs('admin.orders.*'),
        ],
        [
            'label' => 'Conductores',
            'icon' => 'fa-solid fa-user-tie',
            'route' => route('admin.drivers.index'),
            'active' => request()->routeIs('admin.drivers.*'),
        ],
        [
            'label' => 'Envíos',
            'icon' => 'fa-solid fa-truck-fast',
            'route' => route('admin.shipments.index'),
            'active' => request()->routeIs('admin.shipments.*'),
        ],
        [
            'label' => 'Usuarios',
            'icon' => 'fa-solid fa-users-gear',
            'route' => route('admin.users.index'),
            'active' => request()->routeIs('admin.users.*'),
        ]
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

        <!-- Security Seals -->
        <div class="mt-8 border-t pt-4 border-gray-200">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 text-center">Entorno Seguro</h3>
            <div class="flex justify-center space-x-3 text-gray-400">
                <!-- SSL Seal -->
                <div class="flex flex-col items-center" title="SSL Encrypted">
                    <i class="fa-solid fa-lock text-xl mb-1 text-emerald-500"></i>
                    <span class="text-[10px] uppercase font-bold text-gray-400">SSL</span>
                </div>
                <!-- CSRF/XSS Seal -->
                <div class="flex flex-col items-center" title="XSS/CSRF Protected">
                    <i class="fa-solid fa-shield-halved text-xl mb-1 text-emerald-500"></i>
                    <span class="text-[10px] uppercase font-bold text-gray-400">SAFE</span>
                </div>
                <!-- Data Security Seal -->
                <div class="flex flex-col items-center" title="Data Privacy">
                    <i class="fa-solid fa-server text-xl mb-1 text-emerald-500"></i>
                    <span class="text-[10px] uppercase font-bold text-gray-400">DATA</span>
                </div>
            </div>
        </div>
    </div>
</aside>
