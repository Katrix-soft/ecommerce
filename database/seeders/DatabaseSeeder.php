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

        // 2. Solo generar productos si la tabla está vacía para evitar duplicados y lentitud
        if (Product::count() > 0) {
            $this->command->info('Las categorías y opciones fueron verificadas. Ya existen productos en la base de datos, saltando generación de productos de prueba.');
            return;
        }

        Storage::deleteDirectory('products');
        Storage::makeDirectory('products');

        Product::factory(1550)->create()->each(function ($product) {
            // Crear variantes con características aleatorias para probar filtros
            $features = \App\Models\Feature::all()->random(rand(1, 3));
            $variant = $product->variants()->create([
                'sku' => $product->sku . '-V1',
                'stock' => 100,
                'price' => $product->price,
            ]);
            $variant->features()->attach($features);
        });
    }
}