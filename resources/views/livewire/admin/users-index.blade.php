<div>
    <!-- Header with premium gradient border -->
    <div class="mb-6 bg-gradient-to-r from-purple-500/10 via-indigo-500/10 to-blue-500/10 p-6 rounded-2xl border border-indigo-100 shadow-sm backdrop-blur-md flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-700 to-indigo-800 bg-clip-text text-transparent">Gestión de Usuarios</h1>
            <p class="text-sm text-gray-500 mt-1">Administra usuarios, asigna roles y controla los accesos del sistema.</p>
        </div>
        <button wire:click="openCreateModal"
            class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
            <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
        </button>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Usuarios</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ \App\Models\User::count() }}</h3>
                </div>
                <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                    <i class="fa-solid fa-users text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Administradores</p>
                    <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ \App\Models\User::role('admin')->count() }}</h3>
                </div>
                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                    <i class="fa-solid fa-user-shield text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Clientes</p>
                    <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ \App\Models\User::role('customer')->count() }}</h3>
                </div>
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 132. Buscador -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <div class="relative max-w-lg">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" wire:model.live="search" placeholder="Buscar por nombre, email..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200/50 transition duration-200 text-sm">
        </div>
    </div>

    <!-- 131. Listado de Usuarios -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Usuario</th>
                        <th class="py-4 px-6">Email</th>
                        <th class="py-4 px-6 text-center">Rol</th>
                        <th class="py-4 px-6 text-center">Pedidos</th>
                        <th class="py-4 px-6">Registro</th>
                        <th class="py-4 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse ($users as $user)
                        <tr class="hover:bg-indigo-50/20 transition-all duration-150">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-base rounded-full shadow-inner border border-indigo-100/50">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                        @if($user->id === auth()->id())
                                            <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded-full">TÚ</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-gray-600">
                                {{ $user->email }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @php
                                    $role = $user->roles->first();
                                    $roleBadges = [
                                        'admin' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                        'customer' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    ];
                                    $roleTexts = [
                                        'admin' => 'Administrador',
                                        'customer' => 'Cliente',
                                    ];
                                    $badgeClass = $roleBadges[$role?->name ?? ''] ?? 'bg-gray-50 text-gray-500 border-gray-200';
                                    $roleText = $roleTexts[$role?->name ?? ''] ?? 'Sin Rol';
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeClass }}">
                                    {{ $roleText }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center font-bold text-gray-700">
                                {{ $user->orders()->count() }}
                            </td>
                            <td class="py-4 px-6 text-gray-500 text-xs">
                                {{ $user->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- 133. Asignar Rol -->
                                    <button wire:click="openRoleModal({{ $user->id }})" title="Asignar Rol"
                                        class="p-2 text-amber-600 hover:bg-amber-50 rounded-xl transition duration-150">
                                        <i class="fa-solid fa-user-tag"></i>
                                    </button>
                                    <button wire:click="openEditModal({{ $user->id }})" title="Editar"
                                        class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition duration-150">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button onclick="confirmDeleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')" title="Eliminar"
                                            class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition duration-150">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-users-slash text-3xl mb-2 text-gray-300"></i>
                                    <span>No se encontraron usuarios.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Create/Edit User Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100">
                    <div class="bg-gradient-to-r from-indigo-700 to-purple-800 px-6 py-4 text-white flex justify-between items-center">
                        <h3 class="text-base font-bold">{{ $isEditMode ? 'Editar Usuario' : 'Nuevo Usuario' }}</h3>
                        <button wire:click="closeModal" class="text-indigo-100 hover:text-white transition duration-150">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveUser">
                        <div class="p-6 space-y-4">
                            <!-- Name -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nombre Completo</label>
                                <input type="text" wire:model="name" placeholder="Ej: Juan Pérez"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200/50 transition">
                                @error('name') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Email</label>
                                <input type="email" wire:model="email" placeholder="Ej: usuario@email.com"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200/50 transition">
                                @error('email') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                    Contraseña @if($isEditMode) <span class="text-gray-400 normal-case">(dejar en blanco para no cambiar)</span> @endif
                                </label>
                                <input type="password" wire:model="password" placeholder="Mínimo 8 caracteres"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200/50 transition">
                                @error('password') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Password confirmation -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Confirmar Contraseña</label>
                                <input type="password" wire:model="password_confirmation" placeholder="Repetir contraseña"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200/50 transition">
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-2 rounded-b-3xl">
                            <button type="button" wire:click="closeModal"
                                class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition duration-150 shadow-sm">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 rounded-xl shadow-md transition duration-150">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- 133. Assign Role Modal -->
    @if($showRoleModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="role-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeRoleModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full border border-gray-100">
                    <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-6 py-4 text-white flex justify-between items-center">
                        <div>
                            <h3 class="text-base font-bold">Asignar Rol</h3>
                            <p class="text-xs text-amber-100 mt-0.5">{{ $roleUserName }}</p>
                        </div>
                        <button wire:click="closeRoleModal" class="text-amber-100 hover:text-white transition duration-150">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="assignRole">
                        <div class="p-6">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Seleccionar Rol</label>
                            <div class="space-y-3">
                                @foreach($roles as $role)
                                    <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all duration-150
                                        {{ $selectedRole === $role->name ? 'border-indigo-300 bg-indigo-50 shadow-sm' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
                                        <input type="radio" wire:model="selectedRole" value="{{ $role->name }}"
                                            class="text-indigo-600 focus:ring-indigo-500">
                                        <div>
                                            <span class="font-semibold text-sm text-gray-800 capitalize">
                                                @if($role->name === 'admin')
                                                    <i class="fa-solid fa-user-shield text-indigo-600 mr-1"></i> Administrador
                                                @elseif($role->name === 'customer')
                                                    <i class="fa-solid fa-bag-shopping text-emerald-600 mr-1"></i> Cliente
                                                @else
                                                    {{ $role->name }}
                                                @endif
                                            </span>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                @if($role->name === 'admin')
                                                    Acceso completo al panel de administración
                                                @elseif($role->name === 'customer')
                                                    Acceso solo a la tienda y sus pedidos
                                                @else
                                                    Rol personalizado
                                                @endif
                                            </p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('selectedRole') <span class="text-xs text-rose-600 mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-2 rounded-b-3xl">
                            <button type="button" wire:click="closeRoleModal"
                                class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition duration-150 shadow-sm">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 rounded-xl shadow-md transition duration-150">
                                Asignar Rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete confirmation SweetAlert -->
    @push('js')
    <script>
        function confirmDeleteUser(id, name) {
            Swal.fire({
                title: '¿Eliminar Usuario?',
                text: `Estás a punto de remover a ${name} de forma permanente.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('deleteUser', id);
                }
            })
        }
    </script>
    @endpush
</div>
