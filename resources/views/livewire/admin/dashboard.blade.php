<div class="space-y-8">
    {{-- Header / Welcome Banner --}}
    <div class="bg-gradient-to-r from-violet-600 via-indigo-600 to-indigo-700 rounded-none p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <img class="size-14 rounded-none border-2 border-white/20 object-cover shadow-md" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                <div>
                    <h1 class="text-xl font-bold">¡Hola de nuevo, {{ auth()->user()->name }}!</h1>
                    <p class="text-xs text-violet-100 mt-1">Este es el resumen de operaciones y métricas de tu tienda.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 bg-white/10 px-3.5 py-1.5 rounded-none border border-white/10 self-start md:self-auto text-xs font-semibold">
                <i class="fa-solid fa-chart-pie"></i>
                Panel de Control Activo
            </div>
        </div>
    </div>

    @if (empty($enabledMetrics))
        {{-- Empty State when no metrics are enabled --}}
        <div class="bg-white rounded-none border border-gray-100 p-12 text-center shadow-sm">
            <div class="w-16 h-16 bg-gray-50 rounded-none flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-chart-line text-2xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-700 mb-2">No hay métricas habilitadas</h3>
            <p class="text-sm text-gray-500 max-w-md mx-auto">El Administrador General aún no ha configurado métricas visibles para tu cuenta. Los indicadores y reportes aparecerán aquí una vez habilitados.</p>
        </div>
    @else
        {{-- KPI Cards Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {{-- Ingresos Totales --}}
            @if (in_array('ingresos_totales', $enabledMetrics))
                <div class="bg-white rounded-none border border-gray-100 border-l-4 border-l-violet-600 p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ingresos Totales</p>
                            <h3 class="text-2xl font-black text-gray-800 mt-2">${{ number_format($data['ingresos_totales'] ?? 0, 2, ',', '.') }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-violet-50 text-violet-600 flex items-center justify-center rounded-none text-sm font-semibold">
                            <i class="fa-solid fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Ticket Promedio --}}
            @if (in_array('ticket_promedio', $enabledMetrics))
                <div class="bg-white rounded-none border border-gray-100 border-l-4 border-l-purple-600 p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ticket Promedio</p>
                            <h3 class="text-2xl font-black text-gray-800 mt-2">${{ number_format($data['ticket_promedio'] ?? 0, 2, ',', '.') }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-purple-50 text-purple-600 flex items-center justify-center rounded-none text-sm font-semibold">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Ventas vs Mes Anterior --}}
            @if (in_array('ventas_vs_mes_anterior', $enabledMetrics) && isset($data['ventas_vs_mes_anterior']))
                <div class="bg-white rounded-none border border-gray-100 border-l-4 border-l-fuchsia-600 p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ventas vs Mes Ant.</p>
                            <h3 class="text-2xl font-black text-gray-800 mt-2">${{ number_format($data['ventas_vs_mes_anterior']['this_month'] ?? 0, 0, ',', '.') }}</h3>
                            <p class="text-xs font-semibold mt-2 flex items-center gap-1 {{ $data['ventas_vs_mes_anterior']['direction'] === 'up' ? 'text-emerald-600' : 'text-rose-600' }}">
                                <i class="fa-solid {{ $data['ventas_vs_mes_anterior']['direction'] === 'up' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                <span>{{ $data['ventas_vs_mes_anterior']['percentage'] }}%</span>
                                <span class="text-gray-400 font-normal">vs mes anterior</span>
                            </p>
                        </div>
                        <div class="w-10 h-10 bg-fuchsia-50 text-fuchsia-600 flex items-center justify-center rounded-none text-sm font-semibold">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Órdenes del Día --}}
            @if (in_array('ordenes_del_dia', $enabledMetrics))
                <div class="bg-white rounded-none border border-gray-100 border-l-4 border-l-emerald-600 p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Órdenes del Día</p>
                            <h3 class="text-2xl font-black text-gray-800 mt-2">{{ $data['ordenes_del_dia'] ?? 0 }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 flex items-center justify-center rounded-none text-sm font-semibold">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Órdenes Pendientes --}}
            @if (in_array('ordenes_pendientes', $enabledMetrics))
                <div class="bg-white rounded-none border border-gray-100 border-l-4 border-l-amber-600 p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Órdenes Pendientes</p>
                            <h3 class="text-2xl font-black text-gray-800 mt-2">{{ $data['ordenes_pendientes'] ?? 0 }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-amber-50 text-amber-600 flex items-center justify-center rounded-none text-sm font-semibold">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Órdenes Canceladas --}}
            @if (in_array('ordenes_canceladas', $enabledMetrics))
                <div class="bg-white rounded-none border border-gray-100 border-l-4 border-l-rose-600 p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Órdenes Canceladas</p>
                            <h3 class="text-2xl font-black text-gray-800 mt-2">{{ $data['ordenes_canceladas'] ?? 0 }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-rose-50 text-rose-600 flex items-center justify-center rounded-none text-sm font-semibold">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Envíos Activos --}}
            @if (in_array('envios_activos', $enabledMetrics))
                <div class="bg-white rounded-none border border-gray-100 border-l-4 border-l-blue-600 p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Envíos Activos</p>
                            <h3 class="text-2xl font-black text-gray-800 mt-2">{{ $data['envios_activos'] ?? 0 }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 flex items-center justify-center rounded-none text-sm font-semibold">
                            <i class="fa-solid fa-truck"></i>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tiempo Entrega Promedio --}}
            @if (in_array('tiempo_entrega_prom', $enabledMetrics))
                <div class="bg-white rounded-none border border-gray-100 border-l-4 border-l-cyan-600 p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tiempo Entrega Prom.</p>
                            <h3 class="text-2xl font-black text-gray-800 mt-2">{{ $data['tiempo_entrega_prom'] ?? 'N/A' }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-cyan-50 text-cyan-600 flex items-center justify-center rounded-none text-sm font-semibold">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Conductores Activos --}}
            @if (in_array('conductores_activos', $enabledMetrics))
                <div class="bg-white rounded-none border border-gray-100 border-l-4 border-l-teal-600 p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Conductores Activos</p>
                            <h3 class="text-2xl font-black text-gray-800 mt-2">{{ $data['conductores_activos'] ?? 0 }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-teal-50 text-teal-600 flex items-center justify-center rounded-none text-sm font-semibold">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Nuevos Registros --}}
            @if (in_array('nuevos_registros', $enabledMetrics))
                <div class="bg-white rounded-none border border-gray-100 border-l-4 border-l-indigo-600 p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nuevos Clientes (30d)</p>
                            <h3 class="text-2xl font-black text-gray-800 mt-2">{{ $data['nuevos_registros'] ?? 0 }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 flex items-center justify-center rounded-none text-sm font-semibold">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Clientes Recurrentes --}}
            @if (in_array('clientes_recurrentes', $enabledMetrics))
                <div class="bg-white rounded-none border border-gray-100 border-l-4 border-l-sky-600 p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Clientes Recurrentes</p>
                            <h3 class="text-2xl font-black text-gray-800 mt-2">{{ $data['clientes_recurrentes'] ?? 0 }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-sky-50 text-sky-600 flex items-center justify-center rounded-none text-sm font-semibold">
                            <i class="fa-solid fa-arrows-spin"></i>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Advanced Panels Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Ventas Chart (7 days) --}}
            @if (in_array('grafico_ventas', $enabledMetrics) && isset($data['grafico_ventas']))
                <div class="bg-white rounded-none border border-gray-100 p-6 shadow-sm lg:col-span-8 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Ventas por Día (Últimos 7 días)</h3>
                            <span class="text-xs bg-violet-50 text-violet-600 font-semibold px-2 py-0.5 rounded-none border border-violet-100">Gráfico</span>
                        </div>
                        {{-- Pure CSS Bar Chart --}}
                        <div class="flex items-end justify-between gap-4 h-48 px-2 mt-6">
                            @foreach ($data['grafico_ventas'] as $day)
                                <div class="flex-1 flex flex-col items-center h-full justify-end group">
                                    <div class="relative w-full flex justify-center">
                                        <span class="absolute -top-7 text-xs font-black text-violet-600 bg-violet-50 border border-violet-100 px-1 py-0.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-none shadow-sm pointer-events-none">
                                            ${{ number_format($day['value'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div 
                                        style="height: {{ max($day['percentage'], 4) }}%" 
                                        class="w-full bg-gradient-to-t from-indigo-500 to-violet-500 hover:from-indigo-600 hover:to-violet-600 transition-all duration-300 rounded-none cursor-pointer relative shadow-sm"
                                    ></div>
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mt-2.5 block text-center truncate w-full">{{ $day['label'] }}</span>
                                    <span class="text-[9px] text-gray-400 font-semibold block text-center">{{ $day['date'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Products / Users details --}}
            @php
                $showProductsPanel = in_array('mas_vendidos', $enabledMetrics) || in_array('stock_bajo', $enabledMetrics) || in_array('mas_visitados', $enabledMetrics);
                $colsCount = in_array('grafico_ventas', $enabledMetrics) ? 'lg:col-span-4' : 'lg:col-span-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
            @endphp

            @if ($showProductsPanel)
                <div class="{{ $colsCount }} space-y-6">
                    {{-- Más Vendidos --}}
                    @if (in_array('mas_vendidos', $enabledMetrics) && isset($data['mas_vendidos']))
                        <div class="bg-white rounded-none border border-gray-100 border-l-4 border-l-amber-500 shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between bg-gradient-to-r from-amber-50/50 to-white px-5 py-4 border-b border-gray-100">
                                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Productos Más Vendidos</h3>
                                <div class="w-7 h-7 bg-amber-100 text-amber-600 flex items-center justify-center">
                                    <i class="fa-solid fa-crown text-xs"></i>
                                </div>
                            </div>
                            @if (empty($data['mas_vendidos']))
                                <p class="text-xs text-gray-400 text-center py-6">No hay datos de ventas.</p>
                            @else
                                <div class="p-4 space-y-3">
                                    @foreach ($data['mas_vendidos'] as $product)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-100 hover:border-amber-200 hover:bg-amber-50/10 hover:translate-x-1 transition-all duration-200">
                                            <div class="flex-1 min-w-0">
                                                <span class="text-xs font-bold text-gray-800 block truncate">{{ $product['product_name'] }}</span>
                                                @if (!empty($product['features']))
                                                    <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                                        @foreach ($product['features'] as $feature)
                                                            @if (str_starts_with($feature['value'], '#'))
                                                                <span class="inline-block w-3.5 h-3.5 border border-gray-200 shadow-xs" style="background-color: {{ $feature['value'] }};" title="{{ $feature['value'] }}"></span>
                                                            @else
                                                                <span class="inline-flex items-center px-1.5 py-0.5 text-[9px] font-extrabold uppercase bg-gray-200/60 text-gray-600 border border-gray-300/40">{{ $feature['value'] }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2.5">
                                                <span class="text-[10px] font-semibold text-gray-400">{{ $product['quantity'] }} uds.</span>
                                                <span class="text-xs font-black bg-amber-100 text-amber-800 px-2 py-0.5 rounded-none border border-amber-200">#{{ $loop->iteration }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Stock Bajo --}}
                    @if (in_array('stock_bajo', $enabledMetrics) && isset($data['stock_bajo']))
                        <div class="bg-white rounded-none border border-gray-100 border-l-4 border-l-rose-500 shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between bg-gradient-to-r from-rose-50/50 to-white px-5 py-4 border-b border-gray-100">
                                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Alertas de Stock</h3>
                                <div class="w-7 h-7 bg-rose-100 text-rose-600 flex items-center justify-center">
                                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                </div>
                            </div>
                            @if (empty($data['stock_bajo']))
                                <div class="text-center py-8">
                                    <i class="fa-solid fa-circle-check text-emerald-500 text-xl mb-2 block"></i>
                                    <span class="text-xs text-emerald-700 font-bold block">Todo en stock correcto</span>
                                </div>
                            @else
                                <div class="p-4 space-y-3 max-h-[220px] overflow-y-auto pr-1">
                                    @foreach ($data['stock_bajo'] as $variant)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-100 hover:border-rose-200 hover:bg-rose-50/10 hover:translate-x-1 transition-all duration-200">
                                            <div class="flex-1 min-w-0">
                                                <span class="text-xs font-bold text-gray-800 block truncate">{{ $variant['product_name'] }}</span>
                                                @if (!empty($variant['features']))
                                                    <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                                        @foreach ($variant['features'] as $feature)
                                                            @if (str_starts_with($feature['value'], '#'))
                                                                <span class="inline-block w-3.5 h-3.5 border border-gray-200 shadow-xs" style="background-color: {{ $feature['value'] }};" title="{{ $feature['value'] }}"></span>
                                                            @else
                                                                <span class="inline-flex items-center px-1.5 py-0.5 text-[9px] font-extrabold uppercase bg-gray-200/60 text-gray-600 border border-gray-300/40">{{ $feature['value'] }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="px-2.5 py-1 text-xs font-extrabold rounded-none border shadow-xs {{ $variant['stock'] == 0 ? 'bg-red-100 border-red-200 text-red-800 animate-pulse' : 'bg-amber-100 border-amber-200 text-amber-800' }}">
                                                {{ $variant['stock'] }} uds.
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Más Visitados --}}
                    @if (in_array('mas_visitados', $enabledMetrics) && isset($data['mas_visitados']))
                        <div class="bg-white rounded-none border border-gray-100 border-l-4 border-l-blue-500 shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between bg-gradient-to-r from-blue-50/50 to-white px-5 py-4 border-b border-gray-100">
                                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Productos Visitados</h3>
                                <div class="w-7 h-7 bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </div>
                            </div>
                            @if (empty($data['mas_visitados']))
                                <p class="text-xs text-gray-400 text-center py-6">No hay datos de visitas.</p>
                            @else
                                <div class="p-4 space-y-3">
                                    @php
                                        $maxVisits = collect($data['mas_visitados'])->max('visits') ?: 1;
                                    @endphp
                                    @foreach ($data['mas_visitados'] as $product)
                                        @php
                                            $pct = ($product['visits'] / $maxVisits) * 100;
                                        @endphp
                                        <div class="p-3 bg-gray-50 border border-gray-100 hover:border-blue-200 hover:bg-blue-50/10 hover:translate-x-1 transition-all duration-200 relative overflow-hidden group">
                                            {{-- Background progress indicator --}}
                                            <div class="absolute bottom-0 left-0 h-0.5 bg-blue-500/20 group-hover:bg-blue-500/40 transition-all duration-300" style="width: {{ $pct }}%"></div>
                                            
                                            <div class="flex items-center justify-between relative z-10">
                                                <span class="text-xs font-bold text-gray-800 truncate flex-1">{{ $product['name'] }}</span>
                                                <span class="text-xs font-extrabold bg-blue-50 text-blue-700 px-2 py-0.5 rounded-none border border-blue-100 shadow-xs">{{ $product['visits'] }} visitas</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
