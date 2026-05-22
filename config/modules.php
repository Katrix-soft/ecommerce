<?php

/**
 * Definición de todos los módulos controlables del panel admin.
 * El Super Admin puede habilitar/deshabilitar cada módulo por tenant.
 *
 * Cada módulo tiene:
 *  - label: Nombre para mostrar en la UI
 *  - icon: Clase de FontAwesome
 *  - group: Grupo visual (catalogo, operaciones, configuracion)
 *  - group_label: Label del grupo para la UI
 *  - routes: Patrones de rutas que cubre el módulo
 *  - description: Descripción breve del módulo
 */

return [

    // ── CATÁLOGO ──
    'families' => [
        'label' => 'Familias',
        'icon' => 'fa-solid fa-box-open',
        'group' => 'catalogo',
        'group_label' => 'Catálogo',
        'routes' => ['admin.families.*'],
        'description' => 'Gestión de familias de productos',
    ],
    'categories' => [
        'label' => 'Categorías',
        'icon' => 'fa-solid fa-tags',
        'group' => 'catalogo',
        'group_label' => 'Catálogo',
        'routes' => ['admin.categories.*'],
        'description' => 'Gestión de categorías de productos',
    ],
    'subcategories' => [
        'label' => 'Subcategorías',
        'icon' => 'fa-solid fa-tag',
        'group' => 'catalogo',
        'group_label' => 'Catálogo',
        'routes' => ['admin.subcategories.*'],
        'description' => 'Gestión de subcategorías de productos',
    ],
    'products' => [
        'label' => 'Productos',
        'icon' => 'fa-solid fa-box',
        'group' => 'catalogo',
        'group_label' => 'Catálogo',
        'routes' => ['admin.products.*'],
        'description' => 'Gestión de productos y variantes',
    ],

    // ── OPERACIONES ──
    'orders' => [
        'label' => 'Órdenes',
        'icon' => 'fa-solid fa-receipt',
        'group' => 'operaciones',
        'group_label' => 'Operaciones',
        'routes' => ['admin.orders.*'],
        'description' => 'Gestión de pedidos y tickets',
    ],
    'shipments' => [
        'label' => 'Envíos',
        'icon' => 'fa-solid fa-truck-fast',
        'group' => 'operaciones',
        'group_label' => 'Operaciones',
        'routes' => ['admin.shipments.*'],
        'description' => 'Gestión de envíos y seguimiento',
    ],
    'drivers' => [
        'label' => 'Conductores',
        'icon' => 'fa-solid fa-user-tie',
        'group' => 'operaciones',
        'group_label' => 'Operaciones',
        'routes' => ['admin.drivers.*'],
        'description' => 'Gestión de conductores de entrega',
    ],

    // ── CONFIGURACIÓN ──
    'options' => [
        'label' => 'Opciones',
        'icon' => 'fa-solid fa-cog',
        'group' => 'configuracion',
        'group_label' => 'Configuración',
        'routes' => ['admin.options.*'],
        'description' => 'Opciones y atributos de productos',
    ],
    'covers' => [
        'label' => 'Portadas',
        'icon' => 'fa-solid fa-image',
        'group' => 'configuracion',
        'group_label' => 'Configuración',
        'routes' => ['admin.covers.*'],
        'description' => 'Gestión de portadas e imágenes destacadas',
    ],
    'users' => [
        'label' => 'Usuarios',
        'icon' => 'fa-solid fa-users-gear',
        'group' => 'configuracion',
        'group_label' => 'Configuración',
        'routes' => ['admin.users.*'],
        'description' => 'Gestión de usuarios y roles',
    ],

];
