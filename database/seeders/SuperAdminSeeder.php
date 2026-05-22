<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Crear el usuario Super Admin y asignarle el rol.
     */
    public function run(): void
    {
        // Asegurar que el rol superadmin existe
        Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

        // Crear o actualizar el usuario Super Admin
        $user = User::firstOrCreate(
            ['email' => 'katrixdevs@gmail.com'],
            [
                'name' => 'Katrix Super Admin',
                'password' => Hash::make('12345678'),
            ]
        );

        // Asignar rol superadmin (quitar roles anteriores)
        $user->syncRoles(['superadmin']);

        $this->command->info('✅ Super Admin creado: katrixdevs@gmail.com / 12345678');
    }
}
