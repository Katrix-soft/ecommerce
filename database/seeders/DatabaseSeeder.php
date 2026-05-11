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

        Storage::deleteDirectory('products');
        Storage::makeDirectory('products');
        // User::factory(10)->create();

        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $this->call([
            FamilySeeder::class,
            OptionSeeder::class,
        ]);

        Product::factory(50)->create()->each(function ($product) {
            // Crear variantes con características aleatorias para probar filtros
            $features = \App\Models\Feature::all()->random(rand(1, 3));
            $variant = $product->variants()->create([
                'sku' => $product->sku . '-V1',
                'stock' => rand(1, 50),
                'price' => $product->price,
            ]);
            $variant->features()->attach($features);
        });
    }
}
