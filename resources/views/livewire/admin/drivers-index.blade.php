<div>
    <!-- Header with premium gradient border -->
    <div class="mb-6 bg-gradient-to-r from-purple-500/10 via-indigo-500/10 to-blue-500/10 p-6 rounded-2xl border border-indigo-100 shadow-sm backdrop-blur-md flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-700 to-indigo-800 bg-clip-text text-transparent">Conductores de Despacho</h1>
            <p class="text-sm text-gray-500 mt-1">Registra y administra los conductores responsables del reparto de pedidos.</p>
        </div>
        <button wire:click="openCreateModal"
            class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
            <i class="fa-solid fa-user-plus"></i> Nuevo Conductor
        </button>
    </div>

    <!-- Filter and Search controls -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <div class="relative max-w-lg">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" wire:model.live="search" placeholder="Buscar por nombre, DNI, licencia..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200/50 transition duration-200 text-sm">
        </div>
    </div>

    <!-- Drivers Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Nombre Completo</th>
                        <th class="py-4 px-6">DNI</th>
                        <th class="py-4 px-6">Teléfono</th>
                        <th class="py-4 px-6">Licencia / Patente</th>
                        <th class="py-4 px-6 text-center">Estado</th>
                        <th class="py-4 px-6 text-center">Envíos Totales</th>
                        <th class="py-4 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse ($drivers as $driver)
                        <tr class="hover:bg-indigo-50/20 transition-all duration-150">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-base rounded-full shadow-inner border border-indigo-100/50">
                                        {{ strtoupper(substr($driver->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $driver->name }}</div>
                                        <div class="text-xs text-gray-400">Registrado el {{ $driver->created_at->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 font-semibold text-gray-600">
                                {{ $driver->dni }}
                            </td>
                            <td class="py-4 px-6 text-gray-600">
                                <a href="tel:{{ $driver->phone }}" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-phone text-xs text-gray-400"></i> {{ $driver->phone }}
                                </a>
                            </td>
                            <td class="py-4 px-6 font-mono text-gray-600 uppercase">
                                {{ $driver->license }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button wire:click="toggleStatus({{ $driver->id }})" title="Cambiar Estado"
                                    class="px-3 py-1 rounded-full text-xs font-semibold border transition duration-150 {{ $driver->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100 hover:bg-emerald-100' : 'bg-gray-50 text-gray-500 border-gray-200 hover:bg-gray-100' }}">
                                    {{ $driver->is_active ? 'Activo' : 'Inactivo' }}
                                </button>
                            </td>
                            <td class="py-4 px-6 text-center font-bold text-gray-700">
                                {{ $driver->shipments()->count() }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="openEditModal({{ $driver->id }})" title="Editar"
                                        class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition duration-150">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button onclick="confirmDelete({{ $driver->id }}, '{{ $driver->name }}')" title="Eliminar"
                                        class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition duration-150">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-user-tie text-3xl mb-2 text-gray-300"></i>
                                    <span>No se encontraron conductores.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50">
            {{ $drivers->links() }}
        </div>
    </div>

    <!-- Create/Edit Driver Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100">
                    <div class="bg-gradient-to-r from-indigo-700 to-purple-800 px-6 py-4 text-white flex justify-between items-center">
                        <h3 class="text-base font-bold">{{ $isEditMode ? 'Editar Conductor' : 'Nuevo Conductor' }}</h3>
                        <button wire:click="closeModal" class="text-indigo-100 hover:text-white transition duration-150">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveDriver">
                        <div class="p-6 space-y-4">
                            <!-- Name -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nombre Completo</label>
                                <input type="text" wire:model="name" placeholder="Ej: Juan Pérez"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200/50 transition">
                                @error('name') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- DNI -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">DNI / Documento</label>
                                <input type="text" wire:model="dni" placeholder="Ej: 12345678"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200/50 transition">
                                @error('dni') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Teléfono de Contacto</label>
                                <input type="text" wire:model="phone" placeholder="Ej: +54911223344"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200/50 transition">
                                @error('phone') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- License Plate / License Number -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Licencia / Patente de Vehículo</label>
                                <input type="text" wire:model="license" placeholder="Ej: AB123CD o Registro 987654"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200/50 transition">
                                @error('license') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Active status checkbox -->
                            <div class="flex items-center gap-2 pt-2">
                                <input type="checkbox" id="is_active" wire:model="is_active"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 size-4">
                                <label for="is_active" class="text-sm font-semibold text-gray-700">Conductor Activo</label>
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

    <!-- Delete confirmation SweetAlert -->
    @push('js')
    <script>
        function confirmDelete(id, name) {
            Swal.fire({
                title: '¿Eliminar Conductor?',
                text: `Estás a punto de remover a ${name} de forma permanente.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('deleteDriver', id);
                }
            })
        }
    </script>
    @endpush
</div>
