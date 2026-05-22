<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\User;
use App\Models\TenantModule;
use App\Models\TenantMetric;

class ModulesManager extends Component
{
    public $selectedUserId = null;
    public $modules = [];
    public $metrics = [];
    public $adminUsers = [];

    public function mount()
    {
        // Obtener todos los admins (no superadmins)
        $this->loadAdminUsers();

        // Si hay admins, seleccionar el primero
        if (count($this->adminUsers) > 0) {
            $this->selectedUserId = $this->adminUsers[0]['id'];
            $this->loadData();
        }
    }

    public function loadAdminUsers()
    {
        $this->adminUsers = User::role('admin')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function updatedSelectedUserId()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->loadModules();
        $this->loadMetrics();
    }

    public function loadModules()
    {
        if (!$this->selectedUserId) {
            $this->modules = [];
            return;
        }

        $allModules = config('modules');
        $enabledModules = TenantModule::where('user_id', $this->selectedUserId)
            ->where('is_enabled', true)
            ->pluck('module')
            ->toArray();

        $grouped = [];
        foreach ($allModules as $key => $module) {
            $group = $module['group'];
            if (!isset($grouped[$group])) {
                $grouped[$group] = [
                    'label' => $module['group_label'],
                    'items' => [],
                ];
            }
            $grouped[$group]['items'][] = [
                'key' => $key,
                'label' => $module['label'],
                'icon' => $module['icon'],
                'description' => $module['description'],
                'is_enabled' => in_array($key, $enabledModules),
            ];
        }

        $this->modules = $grouped;
    }

    public function loadMetrics()
    {
        if (!$this->selectedUserId) {
            $this->metrics = [];
            return;
        }

        $allMetrics = config('dashboard_metrics');
        $enabledMetrics = TenantMetric::where('user_id', $this->selectedUserId)
            ->where('is_enabled', true)
            ->pluck('metric_key')
            ->toArray();

        $grouped = [];
        foreach ($allMetrics as $categoryKey => $category) {
            $items = [];
            $allCategoryEnabled = true;

            foreach ($category['items'] as $itemKey => $item) {
                $isEnabled = in_array($itemKey, $enabledMetrics);
                if (!$isEnabled) {
                    $allCategoryEnabled = false;
                }
                $items[] = [
                    'key' => $itemKey,
                    'label' => $item['label'],
                    'icon' => $item['icon'],
                    'is_enabled' => $isEnabled,
                ];
            }

            $grouped[$categoryKey] = [
                'label' => $category['label'],
                'is_all_enabled' => $allCategoryEnabled && count($items) > 0,
                'items' => $items,
            ];
        }

        $this->metrics = $grouped;
    }

    public function toggleModule(string $moduleKey)
    {
        if (!$this->selectedUserId) {
            return;
        }

        $tenantModule = TenantModule::firstOrCreate(
            [
                'user_id' => $this->selectedUserId,
                'module' => $moduleKey,
            ],
            [
                'is_enabled' => false,
            ]
        );

        $tenantModule->is_enabled = !$tenantModule->is_enabled;
        $tenantModule->save();

        $status = $tenantModule->is_enabled ? 'habilitado' : 'deshabilitado';
        $moduleName = config("modules.{$moduleKey}.label", $moduleKey);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Módulo Actualizado!',
            'text' => "El módulo \"{$moduleName}\" fue {$status}.",
            'confirmButtonColor' => '#7c3aed',
            'timer' => 1500,
            'showConfirmButton' => false,
        ]);

        $this->loadModules();
    }

    public function toggleMetric(string $metricKey)
    {
        if (!$this->selectedUserId) {
            return;
        }

        $tenantMetric = TenantMetric::firstOrCreate(
            [
                'user_id' => $this->selectedUserId,
                'metric_key' => $metricKey,
            ],
            [
                'is_enabled' => false,
            ]
        );

        $tenantMetric->is_enabled = !$tenantMetric->is_enabled;
        $tenantMetric->save();

        $status = $tenantMetric->is_enabled ? 'habilitada' : 'deshabilitada';
        
        // Buscar el label de la métrica
        $metricLabel = $metricKey;
        foreach (config('dashboard_metrics') as $category) {
            if (isset($category['items'][$metricKey])) {
                $metricLabel = $category['items'][$metricKey]['label'];
                break;
            }
        }

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Métrica Actualizada!',
            'text' => "La métrica \"{$metricLabel}\" fue {$status}.",
            'confirmButtonColor' => '#7c3aed',
            'timer' => 1500,
            'showConfirmButton' => false,
        ]);

        $this->loadMetrics();
    }

    /**
     * Alternar todas las métricas de una categoría específica (Botón "todo" de la mockup)
     */
    public function toggleCategoryMetrics(string $categoryKey)
    {
        if (!$this->selectedUserId) {
            return;
        }

        $category = config("dashboard_metrics.{$categoryKey}");
        if (!$category) return;

        $metricKeys = array_keys($category['items']);
        
        // Comprobar si actualmente están todas habilitadas
        $enabledCount = TenantMetric::where('user_id', $this->selectedUserId)
            ->whereIn('metric_key', $metricKeys)
            ->where('is_enabled', true)
            ->count();

        // Si están todas habilitadas, deshabilitamos todas. Si no, habilitamos todas.
        $newState = ($enabledCount < count($metricKeys));

        foreach ($metricKeys as $key) {
            TenantMetric::updateOrCreate(
                ['user_id' => $this->selectedUserId, 'metric_key' => $key],
                ['is_enabled' => $newState]
            );
        }

        $statusMessage = $newState ? 'habilitadas' : 'deshabilitadas';
        $categoryLabel = $category['label'];

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Categoría Actualizada',
            'text' => "Todas las métricas de \"{$categoryLabel}\" fueron {$statusMessage}.",
            'confirmButtonColor' => '#7c3aed',
            'timer' => 1500,
            'showConfirmButton' => false,
        ]);

        $this->loadMetrics();
    }

    /**
     * Habilitar todos los módulos y métricas para el admin seleccionado.
     */
    public function enableAll()
    {
        if (!$this->selectedUserId) return;

        // 1. Módulos
        $allModules = array_keys(config('modules'));
        foreach ($allModules as $moduleKey) {
            TenantModule::updateOrCreate(
                ['user_id' => $this->selectedUserId, 'module' => $moduleKey],
                ['is_enabled' => true]
            );
        }

        // 2. Métricas
        foreach (config('dashboard_metrics') as $category) {
            foreach (array_keys($category['items']) as $metricKey) {
                TenantMetric::updateOrCreate(
                    ['user_id' => $this->selectedUserId, 'metric_key' => $metricKey],
                    ['is_enabled' => true]
                );
            }
        }

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Todo Habilitado!',
            'text' => 'Se habilitaron todos los módulos y métricas para este admin.',
            'confirmButtonColor' => '#7c3aed',
        ]);

        $this->loadData();
    }

    /**
     * Deshabilitar todos los módulos y métricas para el admin seleccionado.
     */
    public function disableAll()
    {
        if (!$this->selectedUserId) return;

        // 1. Módulos
        TenantModule::where('user_id', $this->selectedUserId)
            ->update(['is_enabled' => false]);

        // 2. Métricas
        TenantMetric::where('user_id', $this->selectedUserId)
            ->update(['is_enabled' => false]);

        $this->dispatch('swal', [
            'icon' => 'info',
            'title' => 'Todo Deshabilitado',
            'text' => 'Se deshabilitaron todos los módulos y métricas para este admin.',
            'confirmButtonColor' => '#7c3aed',
        ]);

        $this->loadData();
    }

    public function render()
    {
        return view('livewire.superadmin.modules-manager')
            ->layout('layouts.admin', [
                'breadcrumbs' => [
                    ['name' => 'Super Admin', 'route' => route('superadmin.dashboard')],
                    ['name' => 'Configuración de Tenant'],
                ],
            ]);
    }
}
