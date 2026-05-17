<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $nombres = [
            'Remera básica', 'Pantalón slim', 'Zapatillas urbanas', 'Campera de abrigo',
            'Vestido floral', 'Jean elastizado', 'Buzo con capucha', 'Camisa Oxford',
            'Short deportivo', 'Blazer casual', 'Medias térmicas', 'Cinturón de cuero',
            'Mochila urbana', 'Gorra de béisbol', 'Bufanda de lana', 'Guantes de invierno',
            'Smart TV 55"', 'Auriculares Bluetooth', 'Celular Android', 'Laptop 15"',
            'Tablet 10"', 'Mouse inalámbrico', 'Teclado mecánico', 'Monitor Full HD',
            'Parlante portátil', 'Cámara digital', 'Smartwatch', 'Power Bank 20000mAh',
            'Licuadora potente', 'Cafetera automática', 'Microondas digital', 'Plancha de vapor',
            'Aspiradora robot', 'Aire acondicionado', 'Heladera No Frost', 'Lavarropas automático',
            'Silla gamer', 'Escritorio plegable', 'Estante flotante', 'Lámpara LED',
            'Colchón memory foam', 'Almohada viscoelástica', 'Juego de sábanas', 'Toallas premium',
            'Proteína whey', 'Creatina monohidrato', 'Bicicleta estática', 'Mancuernas ajustables',
            'Crema hidratante', 'Shampoo natural', 'Perfume importado', 'Kit de maquillaje',
        ];

        $name = $nombres[array_rand($nombres)] . ' ' . rand(100, 999);

        $dir = storage_path('app/public/products');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = uniqid('product_') . '.png';
        $filepath = $dir . '/' . $filename;
        $img = imagecreatetruecolor(640, 480);
        $bgColors = [
            [41, 128, 185],
            [39, 174, 96],
            [192, 57, 43],
            [142, 68, 173],
            [44, 62, 80],
            [243, 156, 18],
        ];
        $bg = $bgColors[array_rand($bgColors)];
        $bgColor = imagecolorallocate($img, $bg[0], $bg[1], $bg[2]);
        $textColor = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $bgColor);
        imagestring($img, 5, 200, 230, strtoupper($name), $textColor);
        imagepng($img, $filepath);
        imagedestroy($img);

        $descripciones = [
            'Producto de alta calidad con materiales premium.',
            'Diseño moderno y funcional para el día a día.',
            'Ideal para regalo o uso personal.',
            'Garantía de 12 meses incluida.',
            'Envío gratis a todo el país.',
            'Stock limitado, aprovechá el precio.',
            'El favorito de nuestros clientes.',
            'Resistente y duradero, te va a encantar.',
        ];

        return [
            'sku' => strtoupper(substr(md5(uniqid()), 0, 12)),
            'name' => $name,
            'description' => $descripciones[array_rand($descripciones)],
            'image_path' => 'products/' . $filename,
            'price' => rand(1000, 150000) / 100 * 100,
            'subcategory_id' => Subcategory::inRandomOrder()->first()->id,
        ];
    }
}
