<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Define permisos y roles de la aplicación (Pasos 127 y 128)
     */
    public function run(): void
    {
        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 127. Definir permisos de la aplicación ──
        $permissions = [
            // Gestión de productos
            'products.index',
            'products.create',
            'products.edit',
            'products.delete',

            // Gestión de categorías
            'categories.index',
            'categories.create',
            'categories.edit',
            'categories.delete',

            // Gestión de familias
            'families.index',
            'families.create',
            'families.edit',
            'families.delete',

            // Gestión de subcategorías
            'subcategories.index',
            'subcategories.create',
            'subcategories.edit',
            'subcategories.delete',

            // Gestión de opciones
            'options.index',

            // Gestión de covers
            'covers.index',
            'covers.create',
            'covers.edit',
            'covers.delete',

            // Gestión de órdenes
            'orders.index',
            'orders.edit',
            'orders.cancel',

            // Gestión de conductores
            'drivers.index',
            'drivers.create',
            'drivers.edit',
            'drivers.delete',

            // Gestión de envíos
            'shipments.index',
            'shipments.create',

            // Gestión de usuarios
            'users.index',
            'users.create',
            'users.edit',
            'users.delete',
            'users.assign-role',

            // Acceso al dashboard admin
            'admin.dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ── 128. Definir roles de la aplicación ──

        // Permisos adicionales de Super Admin
        $superadminPermissions = [
            'superadmin.modules',
            'superadmin.dashboard',
        ];

        foreach ($superadminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin: tiene todos los permisos del panel admin
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        // Super Admin: tiene todos los permisos (admin + superadmin)
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $superadminRole->syncPermissions(array_merge($permissions, $superadminPermissions));

        // Cliente: rol por defecto sin permisos de admin
        Role::firstOrCreate(['name' => 'customer']);
    }
}
