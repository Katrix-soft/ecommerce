<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        // Generar imagen placeholder local con GD
        $dir = public_path('storage/products');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = uniqid('product_') . '.png';
        $filepath = $dir . '/' . $filename;

        $img = imagecreatetruecolor(640, 480);
        $bgColors = [
            [41, 128, 185],   // azul
            [39, 174, 96],    // verde
            [192, 57, 43],    // rojo
            [142, 68, 173],   // violeta
            [44, 62, 80],     // oscuro
            [243, 156, 18],   // naranja
        ];
        $bg = $bgColors[array_rand($bgColors)];
        $bgColor = imagecolorallocate($img, $bg[0], $bg[1], $bg[2]);
        $textColor = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $bgColor);
        imagestring($img, 5, 250, 230, strtoupper($name), $textColor);
        imagepng($img, $filepath);
        imagedestroy($img);

        return [
            'sku' => $this->faker->unique()->ean13(),
            'name' => $name,
            'description' => $this->faker->text(),
            'image_path' => 'products/' . $filename,
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'subcategory_id' => Subcategory::all()->random()->id,
        ];
    }
}

