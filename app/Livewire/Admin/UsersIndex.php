<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersIndex extends Component
{
    use WithPagination;

    public $search = '';

    // Modal states
    public $showModal = false;
    public $isEditMode = false;

    // Role modal
    public $showRoleModal = false;
    public $roleUserId = null;
    public $roleUserName = '';
    public $selectedRole = '';

    // Form fields
    public $userId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;

        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function saveUser()
    {
        $rules = [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->userId ?? 'NULL') . ',id',
        ];

        // Password required only on create
        if (!$this->isEditMode) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        $messages = [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Ingresa un email válido.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];

        $this->validate($rules, $messages);

        if ($this->isEditMode) {
            $user = User::findOrFail($this->userId);
            $data = [
                'name' => $this->name,
                'email' => $this->email,
            ];
            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }
            $user->update($data);

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => '¡Usuario Actualizado!',
                'text' => 'Los datos del usuario han sido editados.',
                'confirmButtonColor' => '#7c3aed',
            ]);
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            // Asignar rol customer por defecto
            $user->assignRole('customer');

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => '¡Usuario Creado!',
                'text' => 'El usuario ha sido registrado exitosamente.',
                'confirmButtonColor' => '#7c3aed',
            ]);
        }

        $this->closeModal();
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Acción no permitida',
                'text' => 'No podés eliminar tu propia cuenta.',
                'confirmButtonColor' => '#7c3aed',
            ]);
            return;
        }

        // Check if user has orders
        if ($user->orders()->exists()) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'No se puede eliminar',
                'text' => 'Este usuario tiene pedidos asociados. Podés deshabilitarlo en su lugar.',
                'confirmButtonColor' => '#7c3aed',
            ]);
            return;
        }

        $user->delete();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Usuario Eliminado!',
            'text' => 'El usuario ha sido removido del sistema.',
            'confirmButtonColor' => '#7c3aed',
        ]);
    }

    // ── 133. Asignar Rol ──

    public function openRoleModal($id)
    {
        $user = User::findOrFail($id);
        $this->roleUserId = $user->id;
        $this->roleUserName = $user->name;
        $this->selectedRole = $user->roles->first()?->name ?? '';
        $this->showRoleModal = true;
    }

    public function closeRoleModal()
    {
        $this->showRoleModal = false;
        $this->roleUserId = null;
        $this->roleUserName = '';
        $this->selectedRole = '';
    }

    public function assignRole()
    {
        $this->validate([
            'selectedRole' => 'required|exists:roles,name',
        ], [
            'selectedRole.required' => 'Debés seleccionar un rol.',
            'selectedRole.exists' => 'El rol seleccionado no es válido.',
        ]);

        $user = User::findOrFail($this->roleUserId);

        // Prevent removing own admin role
        if ($user->id === auth()->id() && $this->selectedRole !== 'admin') {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Acción no permitida',
                'text' => 'No podés quitarte el rol de admin a vos mismo.',
                'confirmButtonColor' => '#7c3aed',
            ]);
            return;
        }

        $user->syncRoles([$this->selectedRole]);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Rol Actualizado!',
            'text' => "Se asignó el rol '{$this->selectedRole}' a {$user->name}.",
            'confirmButtonColor' => '#7c3aed',
        ]);

        $this->closeRoleModal();
    }

    public function render()
    {
        $usersQuery = User::with('roles')->orderBy('created_at', 'desc');

        // ── 132. Buscador ──
        if ($this->search) {
            $usersQuery->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $users = $usersQuery->paginate(10);
        $roles = Role::all();

        return view('livewire.admin.users-index', [
            'users' => $users,
            'roles' => $roles,
        ])->layout('layouts.admin', [
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
                ['name' => 'Usuarios'],
            ],
        ]);
    }
}
