<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\TenantModule;
use App\Models\TenantMetric;

class TenantSeeder extends Seeder
{
    /**
     * Crea el tenant/admin por defecto con todos sus módulos y métricas habilitados.
     * Es idempotente: usa firstOrCreate para no duplicar datos.
     */
    public function run(): void
    {
        // ── 1. Crear admin por defecto ────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@katrix.com.ar'],
            [
                'name'       => 'Katrix Admin',
                'password'   => Hash::make('katrix2024'),
                'store_name' => 'Shoply Demo',
                'store_status' => 'active',
                'store_currency' => 'ARS',
                'max_products' => 100,
                'max_users' => 10,
                'max_orders_per_month' => 500,
            ]
        );

        $admin->syncRoles(['admin']);

        $this->command->info("✅ Admin creado: admin@katrix.com.ar / katrix2024");

        // ── 2. Habilitar todos los módulos para este admin ────────────────────
        $allModules = array_keys(config('modules', []));
        foreach ($allModules as $moduleKey) {
            TenantModule::updateOrCreate(
                ['user_id' => $admin->id, 'module' => $moduleKey],
                ['is_enabled' => true]
            );
        }

        $this->command->info("✅ Módulos habilitados: " . implode(', ', $allModules));

        // ── 3. Habilitar todas las métricas para este admin ───────────────────
        $dashboardMetrics = config('dashboard_metrics', []);
        foreach ($dashboardMetrics as $category) {
            foreach (array_keys($category['items'] ?? []) as $metricKey) {
                TenantMetric::updateOrCreate(
                    ['user_id' => $admin->id, 'metric_key' => $metricKey],
                    ['is_enabled' => true]
                );
            }
        }

        $this->command->info("✅ TenantSeeder completado para admin@katrix.com.ar");
    }
}
