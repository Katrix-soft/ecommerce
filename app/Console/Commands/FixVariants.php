<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Option;
use App\Models\Feature;
use App\Models\Variant;

class FixVariants extends Command
{
    protected $signature = 'fix:variants';
    protected $description = 'Asigna opciones y variantes a los productos según su familia';

    public function handle()
    {
        $this->info('Iniciando proceso de asignación de variantes...');

        $optionTalle = Option::where('name', 'Talle')->first();
        $optionColor = Option::where('name', 'Color')->first();
        $optionSexo = Option::where('name', 'Sexo')->first();

        Product::chunk(100, function ($products) use ($optionTalle, $optionColor, $optionSexo) {
            foreach ($products as $product) {
                $family = $product->subcategory->category->family->name;

                // Limpiar previo
                $product->options()->detach();
                $product->variants()->each(fn($v) => $v->features()->detach());
                $product->variants()->delete();

                $selectedOptions = [];

                if (str_contains($family, 'Moda')) {
                    $selectedOptions = array_filter([$optionTalle, $optionColor, $optionSexo]);
                } elseif (in_array($family, ['Tecnología', 'Hogar', 'Muebles', 'Deportes'])) {
                    $selectedOptions = array_filter([$optionColor]);
                } else {
                    $selectedOptions = array_filter([$optionColor]);
                }

                // Adjuntar opciones al producto
                foreach ($selectedOptions as $option) {
                    $product->options()->attach($option->id, [
                        'feature_id' => $option->features->pluck('id')->toArray()
                    ]);
                }

                // Crear 2 variantes de ejemplo para cada producto
                for ($i = 1; $i <= 2; $i++) {
                    $variantFeatures = [];
                    foreach ($selectedOptions as $option) {
                        $variantFeatures[] = $option->features->random()->id;
                    }

                    // Asegurarse de que no repetimos la misma combinación exacta (opcional, pero ayuda)
                    $variant = $product->variants()->create([
                        'sku' => $product->sku . '-V' . $i,
                        'image_path' => null,
                    ]);
                    $variant->features()->attach(array_unique($variantFeatures));
                }
            }
            $this->info('Lote de 100 productos procesado...');
        });

        $this->info('¡Proceso completado!');
    }
}
