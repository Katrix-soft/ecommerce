<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Family;
use App\Models\Subcategory;
use App\Models\Option;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Asegurar que las Familias, Categorías, Subcategorías y Opciones estén creadas (son idempotentes y seguras)
        $this->call([
            FamilySeeder::class,
            OptionSeeder::class,
        ]);

        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // 2. Comentamos la generación masiva con factory para usar el comando ImportFakeStore
        // if (Product::count() > 0) {
        //     $this->command->info('Las categorías y opciones fueron verificadas. Ya existen productos, saltando seeder.');
        //     return;
        // }
    }
}