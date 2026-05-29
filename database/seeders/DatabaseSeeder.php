<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
    }
}