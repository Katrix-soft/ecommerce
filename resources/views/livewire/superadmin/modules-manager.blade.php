<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-violet-200">
                <i class="fa-solid fa-shield-halved text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Panel de Super Admin</h1>
                <p class="text-sm text-gray-500">Configuración global y permisos por Tenant</p>
            </div>
        </div>
    </div>

    {{-- Selector de Admin --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <label for="tenant-select" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fa-solid fa-user-shield text-violet-500 mr-1"></i>
            Seleccionar Admin / Tenant
        </label>
        <select
            id="tenant-select"
            wire:model.live="selectedUserId"
            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 font-medium focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 appearance-none cursor-pointer"
        >
            <option value="">— Seleccioná un admin —</option>
            @foreach ($adminUsers as $admin)
                <option value="{{ $admin['id'] }}">{{ $admin['name'] }} ({{ $admin['email'] }})</option>
            @endforeach
        </select>
    </div>

    @if ($selectedUserId)
        {{-- Acciones Rápidas --}}
        <div class="flex items-center gap-3 mb-8">
            <button
                wire:click="enableAll"
                wire:confirm="¿Habilitar TODOS los módulos y métricas para este admin?"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-700 rounded-xl text-sm font-semibold hover:bg-emerald-100 transition-all duration-200 border border-emerald-200"
            >
                <i class="fa-solid fa-toggle-on text-base"></i>
                Habilitar Todo el Sistema
            </button>
            <button
                wire:click="disableAll"
                wire:confirm="¿Deshabilitar TODOS los módulos y métricas para este admin?"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 text-red-700 rounded-xl text-sm font-semibold hover:bg-red-100 transition-all duration-200 border border-red-200"
            >
                <i class="fa-solid fa-toggle-off text-base"></i>
                Deshabilitar Todo el Sistema
            </button>
        </div>

        {{-- Dos Columnas en Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            
            {{-- Columna 1: Módulos del Admin --}}
            <div class="space-y-6">
                <div class="flex items-center gap-2.5 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-cubes text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Módulos del Admin</h2>
                        <p class="text-xs text-gray-500">Control de funcionalidades por tenant</p>
                    </div>
                </div>

                @foreach ($modules as $groupKey => $group)
                    @php
                        $totalItems = count($group['items']);
                        $enabledCount = collect($group['items'])->where('is_enabled', true)->count();
                    @endphp
                    <div 
                        x-data="{ open: false }" 
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200"
                    >
                        {{-- Cabecera Colapsable --}}
                        <div 
                            x-on:click="open = !open"
                            class="px-6 py-4 flex items-center justify-between cursor-pointer hover:bg-gray-50/50 transition-colors duration-150"
                        >
                            <div class="flex items-center gap-3">
                                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">
                                    {{ $group['label'] }}
                                </h3>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-violet-50 text-violet-600 border border-violet-100">
                                    {{ $enabledCount }} / {{ $totalItems }} activos
                                </span>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <i 
                                    class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200"
                                    :class="open ? 'rotate-180 text-violet-500' : ''"
                                ></i>
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
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 {{ $module['is_enabled'] ? 'bg-violet-100 text-violet-600' : 'bg-gray-100 text-gray-400' }} transition-colors duration-300">
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

            {{-- Columna 2: Métricas del Dashboard --}}
            <div class="space-y-6">
                <div class="flex items-center gap-2.5 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-chart-simple text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Métricas del dashboard</h2>
                        <p class="text-xs text-gray-500">Control por tenant</p>
                    </div>
                </div>

                @foreach ($metrics as $categoryKey => $category)
                    @php
                        $totalMetrics = count($category['items']);
                        $enabledMetricsCount = collect($category['items'])->where('is_enabled', true)->count();
                    @endphp
                    <div 
                        x-data="{ open: false }" 
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200" 
                        wire:key="category-card-{{ $categoryKey }}"
                    >
                        {{-- Cabecera Colapsable --}}
                        <div 
                            x-on:click="open = !open"
                            class="px-6 py-4 flex items-center justify-between cursor-pointer hover:bg-gray-50/50 transition-colors duration-150"
                        >
                            <div class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 rounded-full {{ $categoryKey === 'ventas' ? 'bg-violet-600' : ($categoryKey === 'ordenes' ? 'bg-emerald-600' : ($categoryKey === 'logistica' ? 'bg-amber-600' : ($categoryKey === 'productos' ? 'bg-red-600' : 'bg-blue-600'))) }}"></span>
                                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">
                                    {{ $category['label'] }}
                                </h3>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100">
                                    {{ $enabledMetricsCount }} / {{ $totalMetrics }} activas
                                </span>
                            </div>
                            
                            <div class="flex items-center gap-3" x-on:click.stop>
                                {{-- Botón "todo" --}}
                                <button
                                    wire:click="toggleCategoryMetrics('{{ $categoryKey }}')"
                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition-all duration-200 {{ $category['is_all_enabled'] ? 'bg-indigo-50 border-indigo-200 text-indigo-600 hover:bg-indigo-100' : 'bg-gray-50 border-gray-200 text-gray-500 hover:bg-gray-100' }}"
                                >
                                    todo
                                </button>
                                
                                <div x-on:click="open = !open" class="cursor-pointer py-1 pl-1">
                                    <i 
                                        class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200"
                                        :class="open ? 'rotate-180 text-indigo-500' : ''"
                                    ></i>
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
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 {{ $metric['is_enabled'] ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-400' }} transition-colors duration-300">
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

        {{-- Info Card --}}
        <div class="mt-8 bg-gradient-to-r from-violet-50 to-indigo-50 rounded-2xl p-5 border border-violet-100">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
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
    @else
        {{-- Empty state --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-users-gear text-2xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">Seleccioná un Admin / Tenant</h3>
            <p class="text-sm text-gray-400">Elegí un administrador del dropdown superior para gestionar todos sus privilegios.</p>
        </div>
    @endif
</div>
