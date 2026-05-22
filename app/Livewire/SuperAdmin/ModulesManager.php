<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\TenantModule;
use App\Models\TenantMetric;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ModulesManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Tabs
    public $activeTab = 'modules'; // 'modules', 'store', 'limits', 'users', 'audit'

    // Tenant Selection
    public $selectedUserId = null;
    public $modules = [];
    public $metrics = [];
    public $adminUsers = [];

    // Store Configuration & Scheduled Maintenance
    public $storeName = '';
    public $storeLogo;
    public $storeLogoPath = '';
    public $storeStatus = 'active'; // active, maintenance, suspended
    public $maintenanceMessage = 'La tienda se encuentra en mantenimiento temporal.';
    public $suspendedMessage = 'Esta cuenta ha sido pausada temporalmente por falta de pago.';
    public $storeWhatsapp = '';
    public $storeInstagram = '';
    public $storeEmail = '';
    public $storeCurrency = 'ARS';
    public $maintenanceStartsAt = '';
    public $maintenanceEndsAt = '';

    // Limits & Quotas
    public $maxProducts = 50;
    public $maxUsers = 5;
    public $maxOrdersPerMonth = 100;

    // Billing details
    public $billingPlanPrice = 0.00;
    public $billingNextDueDate = '';
    public $billingCycle = 'monthly';

    // Users Management
    public $searchUser = '';
    public $filterUserRole = ''; // empty means all roles
    public $showUserModal = false;
    public $isEditingUser = false;
    public $formUserId = null;
    public $formUserName = '';
    public $formUserEmail = '';
    public $formUserPassword = '';
    public $formUserDni = '';
    public $formUserRole = 'customer';

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'searchUser' => ['except' => ''],
        'activeTab' => ['except' => 'modules'],
    ];

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
        $this->loadStoreSettings();
        $this->resetPage(); // Reset pagination for users management
    }

    /**
     * Cargar la configuración de la tienda.
     */
    public function loadStoreSettings()
    {
        if (!$this->selectedUserId) {
            $this->storeName = '';
            $this->storeLogoPath = '';
            $this->storeLogo = null;
            $this->storeStatus = 'active';
            $this->maintenanceMessage = 'La tienda se encuentra en mantenimiento temporal.';
            $this->suspendedMessage = 'Esta cuenta ha sido pausada temporalmente por falta de pago.';
            $this->storeWhatsapp = '';
            $this->storeInstagram = '';
            $this->storeEmail = '';
            $this->storeCurrency = 'ARS';
            $this->maintenanceStartsAt = '';
            $this->maintenanceEndsAt = '';
            $this->maxProducts = 50;
            $this->maxUsers = 5;
            $this->maxOrdersPerMonth = 100;
            $this->billingPlanPrice = 0.00;
            $this->billingNextDueDate = '';
            $this->billingCycle = 'monthly';
            return;
        }

        $user = User::find($this->selectedUserId);
        if ($user) {
            $this->storeName = $user->store_name ?? '';
            $this->storeLogoPath = $user->store_logo_path ?? '';
            $this->storeLogo = null;
            $this->storeStatus = $user->store_status ?? 'active';
            $this->maintenanceMessage = $user->maintenance_message ?? 'La tienda se encuentra en mantenimiento temporal.';
            $this->suspendedMessage = $user->suspended_message ?? 'Esta cuenta ha sido pausada temporalmente por falta de pago.';
            $this->storeWhatsapp = $user->store_whatsapp ?? '';
            $this->storeInstagram = $user->store_instagram ?? '';
            $this->storeEmail = $user->store_email ?? '';
            $this->storeCurrency = $user->store_currency ?? 'ARS';
            $this->maintenanceStartsAt = $user->maintenance_starts_at ? $user->maintenance_starts_at->format('Y-m-d\TH:i') : '';
            $this->maintenanceEndsAt = $user->maintenance_ends_at ? $user->maintenance_ends_at->format('Y-m-d\TH:i') : '';
            $this->maxProducts = $user->max_products ?? 50;
            $this->maxUsers = $user->max_users ?? 5;
            $this->maxOrdersPerMonth = $user->max_orders_per_month ?? 100;
            $this->billingPlanPrice = $user->billing_plan_price ?? 0.00;
            $this->billingNextDueDate = $user->billing_next_due_date ? $user->billing_next_due_date->format('Y-m-d') : '';
            $this->billingCycle = $user->billing_cycle ?? 'monthly';
        }
    }

    /**
     * Guardar la configuración de la tienda.
     */
    public function saveStoreSettings()
    {
        if (!$this->selectedUserId) return;

        $this->validate([
            'storeName' => 'required|string|min:3|max:255',
            'storeLogo' => 'nullable|image|max:2048', // 2MB Max
            'storeStatus' => 'required|in:active,maintenance,suspended',
            'maintenanceMessage' => 'required_if:storeStatus,maintenance|nullable|string',
            'suspendedMessage' => 'required_if:storeStatus,suspended|nullable|string',
            'storeWhatsapp' => 'nullable|string|max:50',
            'storeInstagram' => 'nullable|string|max:100',
            'storeEmail' => 'nullable|email|max:150',
            'storeCurrency' => 'required|string|max:10',
        ], [
            'storeName.required' => 'El nombre de la tienda es obligatorio.',
            'storeName.min' => 'El nombre de la tienda debe tener al menos 3 caracteres.',
            'storeLogo.image' => 'El logo debe ser una imagen válida.',
            'storeLogo.max' => 'El logo no debe superar los 2MB.',
            'storeStatus.required' => 'El estado de la tienda es obligatorio.',
            'maintenanceMessage.required_if' => 'El mensaje de mantenimiento es obligatorio cuando la tienda está en este estado.',
            'suspendedMessage.required_if' => 'El mensaje de suspensión es obligatorio cuando la cuenta está pausada.',
            'storeEmail.email' => 'Ingresa un correo electrónico de contacto válido.',
        ]);

        $user = User::findOrFail($this->selectedUserId);
        $user->store_name = $this->storeName;
        $user->store_status = $this->storeStatus;
        $user->maintenance_message = $this->maintenanceMessage;
        $user->suspended_message = $this->suspendedMessage;
        $user->store_whatsapp = $this->storeWhatsapp;
        $user->store_instagram = $this->storeInstagram;
        $user->store_email = $this->storeEmail;
        $user->store_currency = $this->storeCurrency;

        if ($this->storeLogo) {
            // Eliminar logo anterior si existe
            if ($user->store_logo_path) {
                Storage::disk('public')->delete($user->store_logo_path);
            }
            // Guardar nuevo logo
            $path = $this->storeLogo->store('store_logos', 'public');
            $user->store_logo_path = $path;
            $this->storeLogoPath = $path;
            $this->storeLogo = null;
        }

        $user->save();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Configuración Guardada!',
            'text' => 'Los datos de la tienda han sido actualizados con éxito.',
            'confirmButtonColor' => '#7c3aed',
        ]);
    }

    // ── GESTIÓN DE USUARIOS ──

    public function updatingSearchUser()
    {
        $this->resetPage();
    }

    public function openCreateUserModal()
    {
        $this->resetUserForm();
        $this->isEditingUser = false;
        $this->showUserModal = true;
    }

    public function openEditUserModal($id)
    {
        $this->resetUserForm();
        $user = User::findOrFail($id);
        $this->formUserId = $user->id;
        $this->formUserName = $user->name;
        $this->formUserEmail = $user->email;
        $this->formUserDni = $user->dni ?? '';
        $this->formUserRole = $user->roles->first()?->name ?? 'customer';
        
        $this->isEditingUser = true;
        $this->showUserModal = true;
    }

    public function resetUserForm()
    {
        $this->formUserId = null;
        $this->formUserName = '';
        $this->formUserEmail = '';
        $this->formUserPassword = '';
        $this->formUserDni = '';
        $this->formUserRole = 'customer';
        $this->resetErrorBag();
    }

    public function closeUserModal()
    {
        $this->showUserModal = false;
        $this->resetUserForm();
    }

    public function saveUser()
    {
        if (!$this->isEditingUser) {
            $tenant = User::find($this->selectedUserId);
            if ($tenant) {
                $currentUsers = User::count();
                if ($currentUsers >= $tenant->max_users) {
                    $this->dispatch('swal', [
                        'icon' => 'error',
                        'title' => 'Límite de Usuarios Excedido',
                        'text' => "No se pueden crear más usuarios. El plan actual de esta tienda permite un máximo de {$tenant->max_users} usuarios.",
                        'confirmButtonColor' => '#7c3aed',
                    ]);
                    return;
                }
            }
        }

        $rules = [
            'formUserName' => 'required|string|min:3|max:255',
            'formUserEmail' => 'required|email|unique:users,email,' . ($this->formUserId ?? 'NULL') . ',id',
            'formUserRole' => 'required|exists:roles,name',
            'formUserDni' => 'nullable|string|max:20',
        ];

        if (!$this->isEditingUser) {
            $rules['formUserPassword'] = 'required|string|min:8';
        } else {
            $rules['formUserPassword'] = 'nullable|string|min:8';
        }

        $messages = [
            'formUserName.required' => 'El nombre es obligatorio.',
            'formUserName.min' => 'El nombre debe tener al menos 3 caracteres.',
            'formUserEmail.required' => 'El email es obligatorio.',
            'formUserEmail.email' => 'Ingresa un email válido.',
            'formUserEmail.unique' => 'Este email ya está registrado.',
            'formUserRole.required' => 'El rol es obligatorio.',
            'formUserRole.exists' => 'El rol seleccionado no es válido.',
            'formUserPassword.required' => 'La contraseña es obligatoria.',
            'formUserPassword.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];

        $this->validate($rules, $messages);

        if ($this->isEditingUser) {
            $user = User::findOrFail($this->formUserId);
            $oldRole = $user->roles->first()?->name ?? 'customer';
            if ($oldRole !== $this->formUserRole) {
                $this->dispatch('swal', [
                    'icon' => 'error',
                    'title' => 'Acción denegada',
                    'text' => 'No está permitido cambiar el rol de un usuario existente.',
                    'confirmButtonColor' => '#7c3aed',
                ]);
                return;
            }

            $oldValues = $user->only(['name', 'email', 'dni']);
            $data = [
                'name' => $this->formUserName,
                'email' => $this->formUserEmail,
                'dni' => $this->formUserDni ?: null,
            ];
            if (!empty($this->formUserPassword)) {
                $data['password'] = Hash::make($this->formUserPassword);
            }
            $user->update($data);

            \App\Models\AuditLog::log(
                $this->selectedUserId,
                'user_edit',
                "Usuario '{$user->name}' ({$user->email}) actualizado.",
                $oldValues,
                $user->only(['name', 'email', 'dni'])
            );

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => '¡Usuario Actualizado!',
                'text' => 'Los datos del usuario han sido editados.',
                'confirmButtonColor' => '#7c3aed',
            ]);
        } else {
            $user = User::create([
                'name' => $this->formUserName,
                'email' => $this->formUserEmail,
                'password' => Hash::make($this->formUserPassword),
                'dni' => $this->formUserDni ?: null,
            ]);
            $user->assignRole($this->formUserRole);

            \App\Models\AuditLog::log(
                $this->selectedUserId,
                'user_create',
                "Usuario '{$user->name}' ({$user->email}) creado con rol '{$this->formUserRole}'."
            );

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => '¡Usuario Creado!',
                'text' => 'El usuario ha sido registrado exitosamente.',
                'confirmButtonColor' => '#7c3aed',
            ]);
        }

        $this->closeUserModal();
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Acción no permitida',
                'text' => 'No podés eliminar tu propia cuenta.',
                'confirmButtonColor' => '#7c3aed',
            ]);
            return;
        }

        if ($user->orders()->exists()) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'No se puede eliminar',
                'text' => 'Este usuario tiene pedidos asociados.',
                'confirmButtonColor' => '#7c3aed',
            ]);
            return;
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $user->delete();

        \App\Models\AuditLog::log(
            $this->selectedUserId,
            'user_delete',
            "Usuario '{$userName}' ({$userEmail}) eliminado."
        );

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Usuario Eliminado!',
            'text' => 'El usuario ha sido removido del sistema.',
            'confirmButtonColor' => '#7c3aed',
        ]);
    }

    public function toggleUserActive($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Acción no permitida',
                'text' => 'No podés desactivar tu propia cuenta.',
                'confirmButtonColor' => '#7c3aed',
            ]);
            return;
        }

        $user->is_active = !$user->is_active;
        $user->save();

        \App\Models\AuditLog::log(
            $this->selectedUserId,
            'user_toggle_status',
            "Estado del usuario '{$user->name}' cambiado a " . ($user->is_active ? 'activo' : 'inactivo') . "."
        );

        $statusStr = $user->is_active ? 'activada' : 'desactivada';
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Estado Actualizado!',
            'text' => "La cuenta de {$user->name} ha sido {$statusStr}.",
            'confirmButtonColor' => '#7c3aed',
        ]);
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

        \App\Models\AuditLog::log(
            $this->selectedUserId,
            'module_toggle',
            "Módulo \"{$moduleName}\" " . ($tenantModule->is_enabled ? 'habilitado' : 'deshabilitado') . " para el tenant."
        );

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

        \App\Models\AuditLog::log(
            $this->selectedUserId,
            'metric_toggle',
            "Métrica \"{$metricLabel}\" " . ($tenantMetric->is_enabled ? 'habilitada' : 'deshabilitada') . " para el tenant."
        );

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

        \App\Models\AuditLog::log(
            $this->selectedUserId,
            'category_metrics_toggle',
            "Todas las métricas de \"{$categoryLabel}\" fueron " . ($newState ? 'habilitadas' : 'deshabilitadas') . " para el tenant."
        );

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

        \App\Models\AuditLog::log(
            $this->selectedUserId,
            'enable_all',
            "Habilitados todos los módulos y métricas para el tenant."
        );

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

        \App\Models\AuditLog::log(
            $this->selectedUserId,
            'disable_all',
            "Deshabilitados todos los módulos y métricas para el tenant."
        );

        $this->dispatch('swal', [
            'icon' => 'info',
            'title' => 'Todo Deshabilitado',
            'text' => 'Se deshabilitaron todos los módulos y métricas para este admin.',
            'confirmButtonColor' => '#7c3aed',
        ]);

        $this->loadData();
    }

    /**
     * Habilitar el plan básico para el admin seleccionado.
     */
    public function enableBasicPlan()
    {
        if (!$this->selectedUserId) return;

        // Módulos básicos (Catálogo esencial, órdenes, portadas y opciones)
        $basicModules = ['families', 'categories', 'subcategories', 'products', 'orders', 'covers', 'options'];
        
        // Métricas básicas (Ventas básicas, órdenes del día y pendientes, alerta de stock bajo)
        $basicMetrics = ['ingresos_totales', 'ticket_promedio', 'ordenes_del_dia', 'ordenes_pendientes', 'stock_bajo'];

        // 1. Módulos
        TenantModule::where('user_id', $this->selectedUserId)->update(['is_enabled' => false]);
        foreach ($basicModules as $moduleKey) {
            TenantModule::updateOrCreate(
                ['user_id' => $this->selectedUserId, 'module' => $moduleKey],
                ['is_enabled' => true]
            );
        }

        // 2. Métricas
        TenantMetric::where('user_id', $this->selectedUserId)->update(['is_enabled' => false]);
        foreach ($basicMetrics as $metricKey) {
            TenantMetric::updateOrCreate(
                ['user_id' => $this->selectedUserId, 'metric_key' => $metricKey],
                ['is_enabled' => true]
            );
        }

        \App\Models\AuditLog::log(
            $this->selectedUserId,
            'enable_basic_plan',
            "Aplicado el Plan Básico (módulos y métricas estándar) para el tenant."
        );

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Plan Básico Activado!',
            'text' => 'Se configuraron los módulos y métricas del Plan Básico para este admin.',
            'confirmButtonColor' => '#7c3aed',
        ]);

        $this->loadData();
    }

    /**
     * Habilitar el plan premium para el admin seleccionado.
     */
    public function enablePremiumPlan()
    {
        if (!$this->selectedUserId) return;

        // El Plan Premium habilita todos los módulos y métricas disponibles
        $this->enableAll();

        \App\Models\AuditLog::log(
            $this->selectedUserId,
            'enable_premium_plan',
            "Aplicado el Plan Premium (todos los módulos y métricas) para el tenant."
        );

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Plan Premium Activado!',
            'text' => 'Se configuraron todos los módulos y métricas del Plan Premium para este admin.',
            'confirmButtonColor' => '#7c3aed',
        ]);
    }

    public function saveLimitsAndBilling()
    {
        if (!$this->selectedUserId) return;

        $this->validate([
            'maxProducts' => 'required|integer|min:0',
            'maxUsers' => 'required|integer|min:0',
            'maxOrdersPerMonth' => 'required|integer|min:0',
            'billingPlanPrice' => 'required|numeric|min:0',
            'billingNextDueDate' => 'nullable|date',
            'billingCycle' => 'required|in:monthly,yearly,quarterly',
        ], [
            'maxProducts.required' => 'El límite de productos es obligatorio.',
            'maxUsers.required' => 'El límite de usuarios es obligatorio.',
            'maxOrdersPerMonth.required' => 'El límite de pedidos es obligatorio.',
            'billingPlanPrice.required' => 'El precio del plan es obligatorio.',
            'billingCycle.in' => 'El ciclo de facturación no es válido.',
        ]);

        $user = User::findOrFail($this->selectedUserId);
        $oldValues = $user->only([
            'max_products', 'max_users', 'max_orders_per_month',
            'billing_plan_price', 'billing_next_due_date', 'billing_cycle'
        ]);

        $user->max_products = $this->maxProducts;
        $user->max_users = $this->maxUsers;
        $user->max_orders_per_month = $this->maxOrdersPerMonth;
        $user->billing_plan_price = $this->billingPlanPrice;
        $user->billing_next_due_date = $this->billingNextDueDate ?: null;
        $user->billing_cycle = $this->billingCycle;
        $user->save();

        \App\Models\AuditLog::log(
            $user->id,
            'update_limits_and_billing',
            "Límites y facturación para '{$user->name}' actualizados.",
            $oldValues,
            $user->only([
                'max_products', 'max_users', 'max_orders_per_month',
                'billing_plan_price', 'billing_next_due_date', 'billing_cycle'
            ])
        );

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Límites y Facturación Guardados!',
            'text' => 'Los límites de uso y detalles de suscripción han sido actualizados con éxito.',
            'confirmButtonColor' => '#7c3aed',
        ]);
    }

    public function render()
    {
        $usersQuery = User::with('roles')->orderBy('created_at', 'desc');

        if ($this->searchUser) {
            $usersQuery->where(function ($query) {
                $query->where('name', 'like', '%' . $this->searchUser . '%')
                    ->orWhere('email', 'like', '%' . $this->searchUser . '%')
                    ->orWhere('dni', 'like', '%' . $this->searchUser . '%');
            });
        }

        if ($this->filterUserRole) {
            $usersQuery->role($this->filterUserRole);
        }

        $users = $usersQuery->paginate(10);
        $roles = Role::all();

        $auditLogs = [];
        if ($this->activeTab === 'audit' && $this->selectedUserId) {
            $auditLogs = \App\Models\AuditLog::with('user')
                ->where('tenant_id', $this->selectedUserId)
                ->orderBy('created_at', 'desc')
                ->paginate(15, ['*'], 'auditPage');
        }

        return view('livewire.superadmin.modules-manager', [
            'users' => $users,
            'roles' => $roles,
            'auditLogs' => $auditLogs,
        ])
            ->layout('layouts.admin', [
                'breadcrumbs' => [
                    ['name' => 'Super Admin', 'route' => route('superadmin.dashboard')],
                    ['name' => 'Configuración de Tenant'],
                ],
            ]);
    }
}
