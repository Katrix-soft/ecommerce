<?php

/**
 * Definición de las métricas del dashboard controlables por tenant.
 * El Super Admin puede habilitar/deshabilitar cada métrica.
 */

return [
    'ventas' => [
        'label' => 'VENTAS',
        'items' => [
            'ingresos_totales' => [
                'label' => 'Ingresos totales',
                'icon' => 'fa-solid fa-dollar-sign',
            ],
            'ticket_promedio' => [
                'label' => 'Ticket promedio',
                'icon' => 'fa-solid fa-file-invoice',
            ],
            'ventas_vs_mes_anterior' => [
                'label' => 'Ventas vs mes anterior',
                'icon' => 'fa-solid fa-arrow-trend-up',
            ],
            'grafico_ventas' => [
                'label' => 'Gráfico de ventas',
                'icon' => 'fa-solid fa-chart-line',
            ],
        ],
    ],
    'ordenes' => [
        'label' => 'ÓRDENES',
        'items' => [
            'ordenes_del_dia' => [
                'label' => 'Órdenes del día',
                'icon' => 'fa-solid fa-receipt',
            ],
            'ordenes_pendientes' => [
                'label' => 'Órdenes pendientes',
                'icon' => 'fa-solid fa-clock',
            ],
            'ordenes_canceladas' => [
                'label' => 'Órdenes canceladas',
                'icon' => 'fa-solid fa-circle-xmark',
            ],
        ],
    ],
    'logistica' => [
        'label' => 'LOGÍSTICA',
        'items' => [
            'envios_activos' => [
                'label' => 'Envíos activos',
                'icon' => 'fa-solid fa-truck',
            ],
            'tiempo_entrega_prom' => [
                'label' => 'Tiempo de entrega prom.',
                'icon' => 'fa-solid fa-location-dot',
            ],
            'conductores_activos' => [
                'label' => 'Conductores activos',
                'icon' => 'fa-solid fa-user-tie',
            ],
        ],
    ],
    'productos' => [
        'label' => 'PRODUCTOS',
        'items' => [
            'mas_vendidos' => [
                'label' => 'Más vendidos',
                'icon' => 'fa-solid fa-star',
            ],
            'stock_bajo' => [
                'label' => 'Stock bajo',
                'icon' => 'fa-solid fa-triangle-exclamation',
            ],
            'mas_visitados' => [
                'label' => 'Más visitados',
                'icon' => 'fa-solid fa-eye',
            ],
        ],
    ],
    'usuarios' => [
        'label' => 'USUARIOS',
        'items' => [
            'nuevos_registros' => [
                'label' => 'Nuevos registros',
                'icon' => 'fa-solid fa-users',
            ],
            'clientes_recurrentes' => [
                'label' => 'Clientes recurrentes',
                'icon' => 'fa-solid fa-arrows-spin',
            ],
        ],
    ],
];
