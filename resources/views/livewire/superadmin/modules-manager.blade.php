<div>
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-indigo-600 rounded-none flex items-center justify-center text-white shadow-lg shadow-violet-200">
                <i class="fa-solid fa-shield-halved text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Panel de Super Admin</h1>
                <p class="text-sm text-gray-500">Configuración global y permisos por Tenant</p>
            </div>
        </div>
    </div>

    {{-- Selector de Admin --}}
    <div class="bg-white rounded-none shadow-sm border border-gray-100 p-6 mb-8">
        <label for="tenant-select" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fa-solid fa-user-shield text-violet-500 mr-1"></i>
            Seleccionar Admin / Tenant
        </label>
        <select
            id="tenant-select"
            wire:model.live="selectedUserId"
            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-none text-gray-800 font-medium focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 appearance-none cursor-pointer"
        >
            <option value="">— Seleccioná un admin —</option>
            @foreach ($adminUsers as $admin)
                <option value="{{ $admin['id'] }}">{{ $admin['name'] }} ({{ $admin['email'] }})</option>
            @endforeach
        </select>
    </div>

    @if ($selectedUserId)
        {{-- Info Card --}}
        <div class="mb-8 bg-gradient-to-r from-violet-50 to-indigo-50 rounded-none p-5 border border-violet-100">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-violet-100 rounded-none flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fa-solid fa-circle-info text-violet-600 text-sm"></i>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-violet-900 mb-1">¿Cómo funciona el panel?</h4>
                    <p class="text-xs text-violet-700 leading-relaxed">
                        Haz clic en el título de cualquier categoría para desplegar u ocultar los elementos de control.<br>
                        <strong>Módulos:</strong> Habilita o deshabilita accesos en la barra lateral y rutas de administración.<br>
                        <strong>Métricas:</strong> Define cuáles indicadores y gráficos están visibles en el dashboard principal del tenant.<br>
                        El botón <strong>todo</strong> permite alternar de forma masiva el estado de todas las métricas de una categoría específica sin necesidad de desplegarla.
                    </p>
                </div>
            </div>
        </div>

        {{-- Navigation Tabs --}}
        <div class="flex border-b border-gray-200 mb-8 gap-4 overflow-x-auto scrollbar-none">
            <button
                type="button"
                wire:click="$set('activeTab', 'modules')"
                class="pb-3 text-xs font-bold tracking-wider uppercase border-b-2 transition-all duration-150 {{ $activeTab === 'modules' ? 'border-violet-600 text-violet-600' : 'border-transparent text-gray-400 hover:text-gray-600' }}"
            >
                <i class="fa-solid fa-shapes mr-1.5 text-sm"></i> Módulos y Métricas
            </button>
            <button
                type="button"
                wire:click="$set('activeTab', 'store')"
                class="pb-3 text-xs font-bold tracking-wider uppercase border-b-2 transition-all duration-150 {{ $activeTab === 'store' ? 'border-violet-600 text-violet-600' : 'border-transparent text-gray-400 hover:text-gray-600' }}"
            >
                <i class="fa-solid fa-store mr-1.5 text-sm"></i> Configuración de Tienda
            </button>
            <button
                type="button"
                wire:click="$set('activeTab', 'users')"
                class="pb-3 text-xs font-bold tracking-wider uppercase border-b-2 transition-all duration-150 {{ $activeTab === 'users' ? 'border-violet-600 text-violet-600' : 'border-transparent text-gray-400 hover:text-gray-600' }}"
            >
                <i class="fa-solid fa-users-gear mr-1.5 text-sm"></i> Usuarios
            </button>
            <button
                type="button"
                wire:click="$set('activeTab', 'limits')"
                class="pb-3 text-xs font-bold tracking-wider uppercase border-b-2 transition-all duration-150 {{ $activeTab === 'limits' ? 'border-violet-600 text-violet-600' : 'border-transparent text-gray-400 hover:text-gray-600' }}"
            >
                <i class="fa-solid fa-chart-line mr-1.5 text-sm"></i> Límites y Facturación
            </button>
            <button
                type="button"
                wire:click="$set('activeTab', 'audit')"
                class="pb-3 text-xs font-bold tracking-wider uppercase border-b-2 transition-all duration-150 {{ $activeTab === 'audit' ? 'border-violet-600 text-violet-600' : 'border-transparent text-gray-400 hover:text-gray-600' }}"
            >
                <i class="fa-solid fa-clock-rotate-left mr-1.5 text-sm"></i> Auditoría
            </button>
        </div>

        @if ($activeTab === 'modules')
            {{-- Acciones Rápidas --}}
            <div class="flex flex-wrap items-center gap-2 mb-8">
                <button
                    type="button"
                    x-on:click="
                        Swal.fire({
                            title: '¿Aplicar Plan Básico?',
                            text: 'Esto habilitará únicamente los módulos y métricas esenciales del sistema (Catálogo, Órdenes, Portadas, Opciones y KPIs básicos).',
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, aplicar Plan Básico',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#d97706',
                            cancelButtonColor: '#6b7280',
                            customClass: {
                                popup: 'rounded-none',
                                confirmButton: 'rounded-none',
                                cancelButton: 'rounded-none'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $wire.enableBasicPlan();
                            }
                        });
                    "
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 text-amber-700 rounded-none text-sm font-semibold hover:bg-amber-100 transition-all duration-200 border border-amber-200"
                >
                    <i class="fa-solid fa-cube text-base"></i>
                    Plan Básico
                </button>
                <button
                    type="button"
                    x-on:click="
                        Swal.fire({
                            title: '¿Aplicar Plan Premium?',
                            text: 'Esto habilitará absolutamente todos los módulos y métricas del sistema.',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, aplicar Plan Premium',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#7c3aed',
                            cancelButtonColor: '#6b7280',
                            customClass: {
                                popup: 'rounded-none',
                                confirmButton: 'rounded-none',
                                cancelButton: 'rounded-none'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $wire.enablePremiumPlan();
                            }
                        });
                    "
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-50 text-violet-700 rounded-none text-sm font-semibold hover:bg-violet-100 transition-all duration-200 border border-violet-200"
                >
                    <i class="fa-solid fa-crown text-base text-amber-500 animate-pulse"></i>
                    Plan Premium
                </button>

                @php
                    $isAiEnabled = false;
                    if(isset($modules['catalogo'])) {
                        foreach($modules['catalogo']['items'] as $item) {
                            if($item['key'] === 'ai_import') {
                                $isAiEnabled = $item['is_enabled'];
                                break;
                            }
                        }
                    }
                @endphp
                <button
                    type="button"
                    wire:click="toggleModule('ai_import')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-none text-sm font-semibold transition-all duration-200 border {{ $isAiEnabled ? 'bg-blue-50 text-blue-700 hover:bg-blue-100 border-blue-200' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border-gray-200' }}"
                >
                    <i class="fa-solid fa-wand-magic-sparkles text-base {{ $isAiEnabled ? 'text-blue-500 animate-pulse' : 'text-gray-400' }}"></i>
                    {{ $isAiEnabled ? 'Deshabilitar Documentación IA' : 'Documentación IA' }}
                </button>

                @if(auth()->check() && auth()->user()->hasRole('superadmin'))
                    @php
                        $isChatbotEnabled = false;
                        if(isset($modules['configuracion'])) {
                            foreach($modules['configuracion']['items'] as $item) {
                                if($item['key'] === 'chatbot') {
                                    $isChatbotEnabled = $item['is_enabled'];
                                    break;
                                }
                            }
                        }
                    @endphp
                    <button
                        type="button"
                        wire:click="toggleModule('chatbot')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-none text-sm font-semibold transition-all duration-200 border {{ $isChatbotEnabled ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border-emerald-200' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border-gray-200' }}"
                    >
                        <i class="fa-solid fa-robot text-base {{ $isChatbotEnabled ? 'text-emerald-500 animate-pulse' : 'text-gray-400' }}"></i>
                        {{ $isChatbotEnabled ? 'Deshabilitar Chatbot' : 'Habilitar Chatbot' }}
                    </button>
                @endif

                <button
                    type="button"
                    x-on:click="
                        Swal.fire({
                            title: '¿Deshabilitar todo?',
                            text: '¿Deshabilitar TODOS los módulos y métricas para este admin?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, deshabilitar todo',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#6b7280',
                            customClass: {
                                popup: 'rounded-none',
                                confirmButton: 'rounded-none',
                                cancelButton: 'rounded-none'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $wire.disableAll();
                            }
                        });
                    "
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 text-red-700 rounded-none text-sm font-semibold hover:bg-red-100 transition-all duration-200 border border-red-200"
                >
                    <i class="fa-solid fa-toggle-off text-base"></i>
                    Deshabilitar Todo
                </button>
            </div>

            {{-- Módulos y Métricas Apilados en Filas Completas --}}
            <div class="space-y-12 pb-12">
                
                {{-- Columna 1: Módulos del Admin --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2.5 mb-2">
                        <div class="w-8 h-8 rounded-none bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-cubes text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Módulos del Admin</h2>
                            <p class="text-xs text-gray-500">Control de funcionalidades por tenant</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach ($modules as $groupKey => $group)
                            @php
                                $totalItems = count($group['items']);
                                $enabledCount = collect($group['items'])->where('is_enabled', true)->count();
                            @endphp
                            <div 
                                x-data="{ open: false }" 
                                :class="open ? 'col-span-full border-violet-300 shadow-md ring-1 ring-violet-100' : 'col-span-1 border-gray-100 hover:border-violet-300 hover:shadow-md hover:scale-[1.02] cursor-pointer'"
                                class="bg-white rounded-none shadow-sm border overflow-hidden transition-all duration-300"
                            >
                                {{-- Cabecera Colapsable --}}
                                <div 
                                    x-on:click="open = !open"
                                    :class="open ? 'px-6 py-4 flex items-center justify-between bg-gradient-to-r from-violet-50 to-white border-b border-violet-100 border-l-4 border-l-violet-500' : 'p-6 flex flex-col items-center justify-center text-center aspect-square w-full'"
                                    class="cursor-pointer hover:bg-gray-50/50 transition-all duration-150"
                                >
                                    <div class="w-full h-full flex items-center justify-center">
                                        {{-- Closed State --}}
                                        <div x-show="!open" class="flex flex-col items-center justify-center gap-2 h-full w-full">
                                            <div class="w-12 h-12 bg-violet-100 text-violet-600 flex items-center justify-center text-lg">
                                                <i class="{{ $groupKey === 'catalogo' ? 'fa-solid fa-tags' : ($groupKey === 'operaciones' ? 'fa-solid fa-gears' : 'fa-solid fa-sliders') }}"></i>
                                            </div>
                                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mt-1">
                                                {{ $group['label'] }}
                                            </h3>
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-none bg-violet-50 text-violet-600 border border-violet-100">
                                                {{ $enabledCount }} / {{ $totalItems }} activos
                                            </span>
                                            <i class="fa-solid fa-chevron-down text-gray-400 text-sm mt-2"></i>
                                        </div>

                                        {{-- Open State --}}
                                        <div x-show="open" class="flex items-center justify-between w-full">
                                            <div class="flex items-center gap-2">
                                                <i class="{{ $groupKey === 'catalogo' ? 'fa-solid fa-tags' : ($groupKey === 'operaciones' ? 'fa-solid fa-gears' : 'fa-solid fa-sliders') }} text-violet-500 text-sm"></i>
                                                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">
                                                    {{ $group['label'] }}
                                                </h3>
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-none bg-violet-50 text-violet-600 border border-violet-100">
                                                    {{ $enabledCount }} / {{ $totalItems }} activos
                                                </span>
                                            </div>
                                            <i class="fa-solid fa-chevron-down text-violet-500 text-sm rotate-180"></i>
                                        </div>
                                    </div>
                                </div>

                                {{-- Lista de módulos colapsable --}}
                                <div 
                                    x-show="open" 
                                    x-transition
                                    class="border-t border-gray-50 divide-y divide-gray-50"
                                    style="display: none;"
                                >
                                    @foreach ($group['items'] as $module)
                                        <div
                                            class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors duration-150 cursor-pointer"
                                            wire:key="module-{{ $module['key'] }}"
                                            wire:click="toggleModule('{{ $module['key'] }}')"
                                        >
                                            {{-- Toggle Switch --}}
                                            <button
                                                type="button"
                                                role="switch"
                                                aria-checked="{{ $module['is_enabled'] ? 'true' : 'false' }}"
                                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none {{ $module['is_enabled'] ? 'bg-violet-600' : 'bg-gray-200' }}"
                                            >
                                                <span class="sr-only">Toggle {{ $module['label'] }}</span>
                                                <span
                                                    aria-hidden="true"
                                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-lg ring-0 transition-transform duration-300 ease-in-out {{ $module['is_enabled'] ? 'translate-x-5' : 'translate-x-0' }}"
                                                ></span>
                                            </button>

                                            {{-- Ícono --}}
                                            <div class="w-9 h-9 rounded-none flex items-center justify-center flex-shrink-0 {{ $module['is_enabled'] ? 'bg-violet-100 text-violet-600' : 'bg-gray-100 text-gray-400' }} transition-colors duration-300">
                                                <i class="{{ $module['icon'] }} text-sm"></i>
                                            </div>

                                            {{-- Info del módulo --}}
                                            <div class="flex-1 min-w-0">
                                                <span class="text-sm font-semibold block {{ $module['is_enabled'] ? 'text-gray-900' : 'text-gray-400' }} transition-colors duration-300">
                                                    {{ $module['label'] }}
                                                </span>
                                                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $module['description'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Columna 2: Métricas del Dashboard --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2.5 mb-2">
                        <div class="w-8 h-8 rounded-none bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-chart-simple text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Métricas del dashboard</h2>
                            <p class="text-xs text-gray-500">Control por tenant</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        @foreach ($metrics as $categoryKey => $category)
                            @php
                                $totalMetrics = count($category['items']);
                                $enabledMetricsCount = collect($category['items'])->where('is_enabled', true)->count();
                            @endphp
                            <div 
                                x-data="{ open: false }" 
                                :class="open ? 'col-span-full border-indigo-300 shadow-md ring-1 ring-indigo-100' : 'col-span-1 border-gray-100 hover:border-indigo-300 hover:shadow-md hover:scale-[1.02] cursor-pointer'"
                                class="bg-white rounded-none shadow-sm border overflow-hidden transition-all duration-300" 
                                wire:key="category-card-{{ $categoryKey }}"
                            >
                                {{-- Cabecera Colapsable --}}
                                <div 
                                    x-on:click="open = !open"
                                    :class="open ? 'px-6 py-4 flex items-center justify-between bg-gradient-to-r from-indigo-50 to-white border-b border-indigo-100 border-l-4 border-l-indigo-500' : 'p-6 flex flex-col items-center justify-center text-center aspect-square w-full'"
                                    class="cursor-pointer hover:bg-gray-50/50 transition-all duration-150"
                                >
                                    <div class="w-full h-full flex items-center justify-center">
                                        {{-- Closed State --}}
                                        <div x-show="!open" class="flex flex-col items-center justify-center gap-2 h-full w-full">
                                            <div class="w-12 h-12 bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg">
                                                <i class="{{ $categoryKey === 'ventas' ? 'fa-solid fa-chart-line' : ($categoryKey === 'ordenes' ? 'fa-solid fa-cart-shopping' : ($categoryKey === 'logistica' ? 'fa-solid fa-truck' : ($categoryKey === 'productos' ? 'fa-solid fa-box' : 'fa-solid fa-users'))) }}"></i>
                                            </div>
                                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mt-1">
                                                {{ $category['label'] }}
                                            </h3>
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-none bg-indigo-50 text-indigo-600 border border-indigo-100">
                                                {{ $enabledMetricsCount }} / {{ $totalMetrics }} activas
                                            </span>
                                            <div class="flex items-center gap-2 mt-2" x-on:click.stop>
                                                <button
                                                    wire:click="toggleCategoryMetrics('{{ $categoryKey }}')"
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-none border transition-all duration-200 {{ $category['is_all_enabled'] ? 'bg-indigo-50 border-indigo-200 text-indigo-600 hover:bg-indigo-100' : 'bg-gray-50 border-gray-200 text-gray-500 hover:bg-gray-100' }}"
                                                >
                                                    todo
                                                </button>
                                                <i class="fa-solid fa-chevron-down text-gray-400 text-sm"></i>
                                            </div>
                                        </div>

                                        {{-- Open State --}}
                                        <div x-show="open" class="flex items-center justify-between w-full">
                                            <div class="flex items-center gap-2">
                                                <i class="{{ $categoryKey === 'ventas' ? 'fa-solid fa-chart-line' : ($categoryKey === 'ordenes' ? 'fa-solid fa-cart-shopping' : ($categoryKey === 'logistica' ? 'fa-solid fa-truck' : ($categoryKey === 'productos' ? 'fa-solid fa-box' : 'fa-solid fa-users'))) }} text-indigo-500 text-sm"></i>
                                                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">
                                                    {{ $category['label'] }}
                                                </h3>
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-none bg-indigo-50 text-indigo-600 border border-indigo-100">
                                                    {{ $enabledMetricsCount }} / {{ $totalMetrics }} activas
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-3" x-on:click.stop>
                                                <button
                                                    wire:click="toggleCategoryMetrics('{{ $categoryKey }}')"
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-none border transition-all duration-200 {{ $category['is_all_enabled'] ? 'bg-indigo-50 border-indigo-200 text-indigo-600 hover:bg-indigo-100' : 'bg-gray-50 border-gray-200 text-gray-500 hover:bg-gray-100' }}"
                                                >
                                                    todo
                                                </button>
                                                <i class="fa-solid fa-chevron-down text-indigo-500 text-sm rotate-180" x-on:click="open = !open"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Lista de métricas colapsable --}}
                                <div 
                                    x-show="open" 
                                    x-transition
                                    class="border-t border-gray-50 divide-y divide-gray-50"
                                    style="display: none;"
                                >
                                    @foreach ($category['items'] as $metric)
                                        <div
                                            class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors duration-150 cursor-pointer"
                                            wire:key="metric-{{ $metric['key'] }}"
                                            wire:click="toggleMetric('{{ $metric['key'] }}')"
                                        >
                                            {{-- Toggle Switch --}}
                                            <button
                                                type="button"
                                                role="switch"
                                                aria-checked="{{ $metric['is_enabled'] ? 'true' : 'false' }}"
                                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none {{ $metric['is_enabled'] ? 'bg-indigo-600' : 'bg-gray-200' }}"
                                            >
                                                <span class="sr-only">Toggle {{ $metric['label'] }}</span>
                                                <span
                                                    aria-hidden="true"
                                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-lg ring-0 transition-transform duration-300 ease-in-out {{ $metric['is_enabled'] ? 'translate-x-5' : 'translate-x-0' }}"
                                                ></span>
                                            </button>

                                            {{-- Ícono --}}
                                            <div class="w-9 h-9 rounded-none flex items-center justify-center flex-shrink-0 {{ $metric['is_enabled'] ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-400' }} transition-colors duration-300">
                                                <i class="{{ $metric['icon'] }} text-sm"></i>
                                            </div>

                                            {{-- Info de la métrica --}}
                                            <div class="flex-1 min-w-0">
                                                <span class="text-sm font-semibold block {{ $metric['is_enabled'] ? 'text-gray-900' : 'text-gray-400' }} transition-colors duration-300">
                                                    {{ $metric['label'] }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if ($activeTab === 'store')
            {{-- Tab: Configuración de Tienda --}}
            <div class="bg-white rounded-none shadow-sm border border-gray-100 p-6 animate-fadeIn">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-store text-violet-500"></i> Personalización de la Tienda
                </h3>

                <form wire:submit.prevent="saveStoreSettings" class="space-y-6 max-w-3xl">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Store Name --}}
                        <div>
                            <label for="storeName" class="block text-xs font-bold text-gray-600 uppercase mb-2 tracking-wider">Nombre de la Tienda</label>
                            <input
                                type="text"
                                id="storeName"
                                wire:model="storeName"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-none text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-sm font-semibold"
                                placeholder="Ej. Shoply Store"
                            />
                            @error('storeName') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Default Currency --}}
                        <div>
                            <label for="storeCurrency" class="block text-xs font-bold text-gray-600 uppercase mb-2 tracking-wider">Moneda de la Tienda</label>
                            <select
                                id="storeCurrency"
                                wire:model="storeCurrency"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-none text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-sm font-semibold"
                            >
                                <option value="ARS">ARS ($)</option>
                                <option value="USD">USD (US$)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="UYU">UYU ($U)</option>
                                <option value="CLP">CLP ($)</option>
                            </select>
                            @error('storeCurrency') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Store Status & Message --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50/50 p-4 border border-gray-150">
                        <div class="md:col-span-1">
                            <label for="storeStatus" class="block text-xs font-bold text-gray-600 uppercase mb-2 tracking-wider">Estado de la Cuenta / Tienda</label>
                            <select
                                id="storeStatus"
                                wire:model.live="storeStatus"
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-none text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-sm font-semibold"
                            >
                                <option value="active">Activa (Pública)</option>
                                <option value="maintenance">En Mantenimiento</option>
                                <option value="suspended">Pausada (Falta de Pago)</option>
                            </select>
                            @error('storeStatus') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            @if ($storeStatus === 'maintenance')
                                <label for="maintenanceMessage" class="block text-xs font-bold text-gray-600 uppercase mb-2 tracking-wider">Mensaje de Mantenimiento</label>
                                <textarea
                                    id="maintenanceMessage"
                                    wire:model="maintenanceMessage"
                                    rows="2"
                                    class="w-full px-4 py-2 bg-white border border-gray-200 rounded-none text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-xs font-semibold"
                                    placeholder="Mensaje para los clientes..."
                                ></textarea>
                                @error('maintenanceMessage') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            @elseif ($storeStatus === 'suspended')
                                <label for="suspendedMessage" class="block text-xs font-bold text-gray-600 uppercase mb-2 tracking-wider">Mensaje de Suspensión (Falta de Pago)</label>
                                <textarea
                                    id="suspendedMessage"
                                    wire:model="suspendedMessage"
                                    rows="2"
                                    class="w-full px-4 py-2 bg-white border border-red-200 rounded-none text-red-800 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 text-xs font-semibold"
                                    placeholder="Mensaje sobre suspensión por impago..."
                                ></textarea>
                                @error('suspendedMessage') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            @else
                                <div class="h-full flex items-center text-xs text-emerald-600 font-bold uppercase tracking-wider">
                                    <i class="fa-solid fa-circle-check text-base mr-2"></i> La tienda se encuentra activa y visible al público.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Mantenimiento Programado --}}
                    <div class="space-y-4 pt-2 bg-violet-50/20 p-4 border border-violet-100/50">
                        <h4 class="text-xs font-extrabold uppercase text-violet-800 tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-clock text-violet-550"></i> Programar Ventana de Mantenimiento
                        </h4>
                        <p class="text-[10px] text-gray-500 font-semibold leading-relaxed">
                            Si se configuran estas fechas, la tienda entrará en modo mantenimiento de forma automática entre el período definido, mostrando una cuenta regresiva para los clientes.
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="maintenanceStartsAt" class="block text-xs font-bold text-gray-650 uppercase mb-2 tracking-wider">Fecha / Hora de Inicio</label>
                                <input
                                    type="datetime-local"
                                    id="maintenanceStartsAt"
                                    wire:model="maintenanceStartsAt"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-none text-gray-850 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-xs font-semibold"
                                />
                                @error('maintenanceStartsAt') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="maintenanceEndsAt" class="block text-xs font-bold text-gray-650 uppercase mb-2 tracking-wider">Fecha / Hora de Reapertura</label>
                                <input
                                    type="datetime-local"
                                    id="maintenanceEndsAt"
                                    wire:model="maintenanceEndsAt"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-none text-gray-850 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-xs font-semibold"
                                />
                                @error('maintenanceEndsAt') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Contact Info Section --}}
                    <div class="space-y-4 pt-2">
                        <h4 class="text-xs font-extrabold uppercase text-gray-700 tracking-wider border-b border-gray-100 pb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-info text-gray-400"></i> Información de Contacto Pública
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="storeWhatsapp" class="block text-xs font-bold text-gray-600 uppercase mb-2 tracking-wider">WhatsApp</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                    </span>
                                    <input
                                        type="text"
                                        id="storeWhatsapp"
                                        wire:model="storeWhatsapp"
                                        class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-none text-gray-805 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-xs font-semibold"
                                        placeholder="Ej. +54911223344"
                                    />
                                </div>
                                @error('storeWhatsapp') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="storeInstagram" class="block text-xs font-bold text-gray-600 uppercase mb-2 tracking-wider">Instagram</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fa-brands fa-instagram text-sm"></i>
                                    </span>
                                    <input
                                        type="text"
                                        id="storeInstagram"
                                        wire:model="storeInstagram"
                                        class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-none text-gray-805 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-xs font-semibold"
                                        placeholder="Ej. mi.tienda"
                                    />
                                </div>
                                @error('storeInstagram') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="storeEmail" class="block text-xs font-bold text-gray-600 uppercase mb-2 tracking-wider">Email de Soporte</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fa-solid fa-envelope text-xs"></i>
                                    </span>
                                    <input
                                        type="email"
                                        id="storeEmail"
                                        wire:model="storeEmail"
                                        class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-none text-gray-805 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-xs font-semibold"
                                        placeholder="Ej. soporte@mitienda.com"
                                    />
                                </div>
                                @error('storeEmail') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Store Logo --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-2 tracking-wider">Logo de la Tienda</label>
                        
                        <div class="flex items-center gap-6">
                            {{-- Logo Preview --}}
                            <div class="size-24 border border-dashed border-gray-200 flex items-center justify-center bg-gray-50 overflow-hidden relative">
                                @if ($storeLogo)
                                    <img src="{{ $storeLogo->temporaryUrl() }}" class="object-contain size-full p-2" alt="Temp Logo" />
                                @elseif ($storeLogoPath)
                                    <img src="{{ Storage::url($storeLogoPath) }}" class="object-contain size-full p-2" alt="Store Logo" />
                                @else
                                    <div class="text-center text-gray-400 p-2">
                                        <i class="fa-solid fa-image text-2xl mb-1 block"></i>
                                        <span class="text-[9px] font-extrabold uppercase">Sin logo</span>
                                    </div>
                                @endif
                                <div wire:loading wire:target="storeLogo" class="absolute inset-0 bg-white/70 flex items-center justify-center">
                                    <i class="fa-solid fa-circle-notch animate-spin text-violet-600 text-lg"></i>
                                </div>
                            </div>

                            {{-- File Upload Input --}}
                            <div class="flex-1">
                                <input
                                    type="file"
                                    id="storeLogo"
                                    wire:model="storeLogo"
                                    class="hidden"
                                    accept="image/*"
                                />
                                <label
                                    for="storeLogo"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-violet-50 text-violet-700 text-xs font-extrabold uppercase border border-violet-200 hover:bg-violet-100 cursor-pointer transition-all duration-150"
                                >
                                    <i class="fa-solid fa-cloud-arrow-up"></i> Seleccionar Imagen
                                </label>
                                <p class="text-[10px] text-gray-400 font-semibold mt-2 uppercase tracking-wide">Formatos permitidos: PNG, JPG, WEBP. Máximo 2MB.</p>
                                @error('storeLogo') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Save Button --}}
                    <div class="pt-4 border-t border-gray-150">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 text-white text-xs font-bold uppercase hover:bg-violet-700 transition-all duration-150 shadow-md shadow-violet-100"
                        >
                            <i class="fa-solid fa-floppy-disk text-sm"></i> Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>
        @endif

        @if ($activeTab === 'limits')
            {{-- Tab: Límites y Facturación --}}
            <div class="bg-white rounded-none shadow-sm border border-gray-100 p-6 animate-fadeIn">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 mb-6">
                    <i class="fa-solid fa-chart-line text-violet-500"></i> Límites de Uso y Facturación de Suscripción
                </h3>

                <form wire:submit.prevent="saveLimitsAndBilling" class="space-y-6">
                    {{-- Limits Quotas --}}
                    <div class="space-y-4">
                        <h4 class="text-xs font-extrabold uppercase text-gray-700 tracking-wider border-b border-gray-100 pb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-gauge-high text-gray-400"></i> Cuotas y Límites de Recursos
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="maxProducts" class="block text-xs font-bold text-gray-650 uppercase mb-2 tracking-wider">Límite de Productos</label>
                                <input
                                    type="number"
                                    id="maxProducts"
                                    wire:model="maxProducts"
                                    min="0"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-none text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-sm font-semibold"
                                />
                                @error('maxProducts') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="maxUsers" class="block text-xs font-bold text-gray-650 uppercase mb-2 tracking-wider">Límite de Usuarios</label>
                                <input
                                    type="number"
                                    id="maxUsers"
                                    wire:model="maxUsers"
                                    min="0"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-none text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-sm font-semibold"
                                />
                                @error('maxUsers') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="maxOrdersPerMonth" class="block text-xs font-bold text-gray-650 uppercase mb-2 tracking-wider">Límite de Pedidos / Mes</label>
                                <input
                                    type="number"
                                    id="maxOrdersPerMonth"
                                    wire:model="maxOrdersPerMonth"
                                    min="0"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-none text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-sm font-semibold"
                                />
                                @error('maxOrdersPerMonth') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Billing --}}
                    <div class="space-y-4 pt-4 border-t border-gray-100">
                        <h4 class="text-xs font-extrabold uppercase text-gray-700 tracking-wider border-b border-gray-100 pb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-credit-card text-gray-400"></i> Detalles de Facturación y Suscripción
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="billingPlanPrice" class="block text-xs font-bold text-gray-650 uppercase mb-2 tracking-wider">Precio del Plan</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 font-bold">$</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        id="billingPlanPrice"
                                        wire:model="billingPlanPrice"
                                        min="0"
                                        class="w-full pl-8 pr-4 py-2.5 bg-white border border-gray-200 rounded-none text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-sm font-semibold"
                                    />
                                </div>
                                @error('billingPlanPrice') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="billingNextDueDate" class="block text-xs font-bold text-gray-650 uppercase mb-2 tracking-wider">Fecha Próximo Vencimiento</label>
                                <input
                                    type="date"
                                    id="billingNextDueDate"
                                    wire:model="billingNextDueDate"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-none text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-sm font-semibold"
                                />
                                @error('billingNextDueDate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="billingCycle" class="block text-xs font-bold text-gray-650 uppercase mb-2 tracking-wider">Ciclo de Facturación</label>
                                <select
                                    id="billingCycle"
                                    wire:model="billingCycle"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-none text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 text-sm font-semibold"
                                >
                                    <option value="monthly">Mensual</option>
                                    <option value="quarterly">Trimestral</option>
                                    <option value="yearly">Anual</option>
                                </select>
                                @error('billingCycle') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Save Button --}}
                    <div class="pt-4 border-t border-gray-150">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 text-white text-xs font-bold uppercase hover:bg-violet-700 transition-all duration-150 shadow-md shadow-violet-100"
                        >
                            <i class="fa-solid fa-floppy-disk text-sm"></i> Guardar Límites y Plan
                        </button>
                    </div>
                </form>
            </div>
        @endif

        @if ($activeTab === 'audit')
            {{-- Tab: Auditoría --}}
            <div class="bg-white rounded-none shadow-sm border border-gray-100 p-6 animate-fadeIn">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 mb-6">
                    <i class="fa-solid fa-clock-rotate-left text-violet-600 animate-pulse"></i> Historial de Actividad (Auditoría)
                </h3>

                @if($auditLogs && $auditLogs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200 text-[10px] uppercase font-bold text-gray-400 tracking-wider">
                                    <th class="py-3 px-4">Fecha / Hora</th>
                                    <th class="py-3 px-4">Actor</th>
                                    <th class="py-3 px-4">Acción</th>
                                    <th class="py-3 px-4">Descripción</th>
                                    <th class="py-3 px-4 text-right">Detalles</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs">
                                @foreach($auditLogs as $log)
                                    <tr class="hover:bg-gray-50/50 transition duration-150">
                                        <td class="py-3 px-4 text-gray-500 font-semibold">
                                            {{ $log->created_at->setTimezone('America/Argentina/Buenos_Aires')->format('d/m/Y H:i:s') }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-bold text-gray-800">{{ $log->user ? $log->user->name : 'Sistema / Auto' }}</div>
                                            <div class="text-[9px] text-gray-400 font-semibold">{{ $log->user ? $log->user->email : '' }}</div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-extrabold uppercase bg-gray-100 text-gray-700 tracking-wide">
                                                {{ str_replace('_', ' ', $log->action) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-600 font-semibold">
                                            {{ $log->description }}
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            @if($log->old_values || $log->new_values)
                                                <button
                                                    type="button"
                                                    x-on:click="
                                                        Swal.fire({
                                                            title: 'Cambios Detallados',
                                                            html: '<div class=\'text-left text-xs font-mono bg-slate-900 text-slate-100 p-4 overflow-auto max-h-96 rounded-none\'><b class=\'text-red-400\'>Antes:</b><pre class=\'mb-3\'>' + JSON.stringify(@js($log->old_values), null, 2) + '</pre><b class=\'text-emerald-400\'>Después:</b><pre>' + JSON.stringify(@js($log->new_values), null, 2) + '</pre></div>',
                                                            confirmButtonText: 'Cerrar',
                                                            confirmButtonColor: '#7c3aed',
                                                            customClass: {
                                                                popup: 'rounded-none',
                                                                confirmButton: 'rounded-none'
                                                            }
                                                        })
                                                    "
                                                    class="text-violet-600 hover:text-violet-850 font-bold uppercase tracking-wider text-[10px]"
                                                >
                                                    <i class="fa-solid fa-circle-info"></i> Ver
                                                </button>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $auditLogs->links() }}
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50/50 border border-dashed border-gray-200">
                        <i class="fa-solid fa-clock-rotate-left text-3xl text-gray-350 mb-3 block animate-spin" style="animation-duration: 10s;"></i>
                        <h3 class="text-sm font-bold text-gray-600 mb-1">Sin historial de actividad</h3>
                        <p class="text-xs text-gray-400 max-w-sm mx-auto">No se han registrado acciones o auditorías para este Tenant todavía.</p>
                    </div>
                @endif
            </div>
        @endif

        @if ($activeTab === 'users')
            {{-- Tab: Gestión de Usuarios --}}
            <div class="bg-white rounded-none shadow-sm border border-gray-100 p-6 animate-fadeIn">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-users-gear text-violet-500"></i> Gestión de Usuarios del Sistema
                    </h3>
                    <button
                        type="button"
                        wire:click="openCreateUserModal"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 text-white text-xs font-bold uppercase hover:bg-violet-700 transition-all duration-150 shadow-md shadow-violet-100"
                    >
                        <i class="fa-solid fa-user-plus"></i> Crear Usuario
                    </button>
                </div>

                {{-- Search & Filter --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="md:col-span-3 relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input
                            type="text"
                            wire:model.live="searchUser"
                            placeholder="Buscar usuarios por nombre, email o DNI..."
                            class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-none text-xs font-semibold text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200"
                        />
                    </div>
                    <div class="md:col-span-1">
                        <select
                            wire:model.live="filterUserRole"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-none text-xs font-semibold text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 cursor-pointer"
                        >
                            <option value="">Todos los Roles</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Table --}}
                <div class="border border-gray-150 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-150 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="py-3 px-4">Usuario</th>
                                <th class="py-3 px-4">Email</th>
                                <th class="py-3 px-4">DNI</th>
                                <th class="py-3 px-4 text-center">Rol</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs font-semibold text-gray-700">
                            @forelse ($users as $u)
                                <tr class="hover:bg-gray-50/50 transition duration-150">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 bg-violet-50 text-violet-600 font-bold flex items-center justify-center text-xs border border-violet-100">
                                                {{ strtoupper(substr($u->name, 0, 2)) }}
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <span>{{ $u->name }}</span>
                                                @if ($u->id === auth()->id())
                                                    <span class="text-[9px] font-extrabold text-violet-600 bg-violet-50 border border-violet-100 px-1.5 py-0.5 uppercase tracking-wider">Tú</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-gray-600 font-mono">{{ $u->email }}</td>
                                    <td class="py-3.5 px-4 text-gray-500">{{ $u->dni ?? '—' }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        @php
                                            $roleColor = [
                                                'superadmin' => 'bg-purple-50 text-purple-800 border-purple-100',
                                                'admin' => 'bg-blue-50 text-blue-800 border-blue-100',
                                                'customer' => 'bg-gray-50 text-gray-800 border-gray-200',
                                            ][$u->roles->first()?->name ?? 'customer'] ?? 'bg-gray-50 text-gray-800 border-gray-200';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-extrabold uppercase border {{ $roleColor }}">
                                            {{ $u->roles->first()?->name ?? 'customer' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if ($u->id === auth()->id())
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[9px] font-extrabold uppercase bg-gray-50 text-gray-400 border border-gray-200">
                                                <i class="fa-solid fa-lock text-[8px]"></i> Protegido
                                            </span>
                                        @else
                                            <button
                                                type="button"
                                                wire:click="toggleUserActive({{ $u->id }})"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 text-[9px] font-extrabold uppercase border transition-all duration-150 cursor-pointer rounded-none {{ $u->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' }}"
                                                title="Hacer click para alternar estado"
                                            >
                                                @if ($u->is_active)
                                                    <i class="fa-solid fa-circle-check"></i> Activo
                                                @else
                                                    <i class="fa-solid fa-circle-xmark"></i> Pausado
                                                @endif
                                            </button>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button
                                                type="button"
                                                wire:click="openEditUserModal({{ $u->id }})"
                                                class="p-1.5 text-blue-600 hover:bg-blue-50 border border-transparent hover:border-blue-200 transition duration-150"
                                                title="Editar Usuario"
                                            >
                                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                            </button>
                                            <button
                                                type="button"
                                                x-on:click="
                                                    Swal.fire({
                                                        title: '¿Eliminar usuario?',
                                                        text: '¿Estás seguro de que deseas eliminar al usuario {{ $u->name }}? Esta acción no se puede deshacer.',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonText: 'Sí, eliminar',
                                                        cancelButtonText: 'Cancelar',
                                                        confirmButtonColor: '#ef4444',
                                                        cancelButtonColor: '#6b7280',
                                                        customClass: {
                                                            popup: 'rounded-none',
                                                            confirmButton: 'rounded-none',
                                                            cancelButton: 'rounded-none'
                                                        }
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            $wire.deleteUser({{ $u->id }});
                                                        }
                                                    });
                                                "
                                                class="p-1.5 text-red-600 hover:bg-red-50 border border-transparent hover:border-red-200 transition duration-150"
                                                title="Eliminar Usuario"
                                                @disabled($u->id === auth()->id())
                                            >
                                                <i class="fa-solid fa-trash-can text-xs {{ $u->id === auth()->id() ? 'opacity-40' : '' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-400">
                                        <i class="fa-solid fa-user-slash text-2xl mb-2 block text-gray-300"></i>
                                        <span class="text-xs uppercase font-bold">No se encontraron usuarios</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        @endif

        {{-- Modal de Crear/Editar Usuario --}}
        @if ($showUserModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" aria-hidden="true" wire:click="closeUserModal"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom bg-white rounded-none text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200">
                        <div class="bg-gradient-to-r from-violet-600 to-indigo-700 px-6 py-4 text-white flex justify-between items-center">
                            <h3 class="text-xs font-bold tracking-wider uppercase">
                                {{ $isEditingUser ? 'Editar Usuario' : 'Crear Nuevo Usuario' }}
                            </h3>
                            <button type="button" wire:click="closeUserModal" class="text-white/80 hover:text-white transition duration-150">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <form wire:submit.prevent="saveUser" class="p-6 space-y-4">
                            {{-- Name --}}
                            <div>
                                <label for="formUserName" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">Nombre Completo</label>
                                <input
                                    type="text"
                                    id="formUserName"
                                    wire:model="formUserName"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-none text-xs font-semibold text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200"
                                />
                                @error('formUserName') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="formUserEmail" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">Correo Electrónico</label>
                                <input
                                    type="email"
                                    id="formUserEmail"
                                    wire:model="formUserEmail"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-none text-xs font-semibold text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200"
                                />
                                @error('formUserEmail') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- DNI --}}
                            <div>
                                <label for="formUserDni" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">DNI (Opcional)</label>
                                <input
                                    type="text"
                                    id="formUserDni"
                                    wire:model="formUserDni"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-none text-xs font-semibold text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200"
                                />
                                @error('formUserDni') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Role --}}
                            <div>
                                <label for="formUserRole" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">Rol de Acceso</label>
                                <select
                                    id="formUserRole"
                                    wire:model="formUserRole"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-none text-xs font-semibold text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 disabled:opacity-60 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                    @disabled($isEditingUser)
                                >
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @if ($isEditingUser)
                                    <p class="text-[9px] text-gray-400 font-bold uppercase mt-1">El rol de un usuario existente no se puede cambiar.</p>
                                @endif
                                @error('formUserRole') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="formUserPassword" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">
                                    Contraseña {{ $isEditingUser ? '(Dejar en blanco para no cambiar)' : '' }}
                                </label>
                                <input
                                    type="password"
                                    id="formUserPassword"
                                    wire:model="formUserPassword"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-none text-xs font-semibold text-gray-800 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200"
                                />
                                @error('formUserPassword') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Footer --}}
                            <div class="pt-4 border-t border-gray-150 flex justify-end gap-3">
                                <button
                                    type="button"
                                    wire:click="closeUserModal"
                                    class="px-4 py-2 text-xs font-extrabold uppercase text-gray-600 bg-gray-100 border border-gray-200 hover:bg-gray-200 transition-all duration-150"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 text-xs font-extrabold uppercase text-white bg-violet-600 hover:bg-violet-700 transition-all duration-150"
                                >
                                    Guardar Usuario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @else
        {{-- Empty state --}}
        <div class="bg-white rounded-none shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-none flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-users-gear text-2xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">Seleccioná un Admin / Tenant</h3>
            <p class="text-sm text-gray-400">Elegí un administrador del dropdown superior para gestionar todos sus privilegios.</p>
        </div>
    @endif
</div>
