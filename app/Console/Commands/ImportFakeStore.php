<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Family;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use App\Models\Variant;

class ImportFakeStore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:fakestore {--limit=20 : Límite de productos a traer (máx 20 por defecto)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa productos desde Fake Store API y los guarda en la base de datos local.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');

        $this->info("🚀 Obteniendo {$limit} productos desde Fake Store API...");

        $url = "https://fakestoreapi.com/products?limit={$limit}";

        try {
            $response = Http::get($url);
            
            if ($response->failed()) {
                $this->error("❌ Error al conectar con Fake Store API: " . $response->status());
                return 1;
            }

            $productos = $response->json();

            if (empty($productos)) {
                $this->warn("❌ No se encontraron productos.");
                return 0;
            }

            $contadorNuevos = 0;
            $contadorActualizados = 0;

            foreach ($productos as $prod) {
                $id = $prod['id'] ?? null;
                $titulo = $prod['title'] ?? 'Sin título';
                $precio = $prod['price'] ?? 0;
                $descripcion = $prod['description'] ?? '';
                $categoriaRaw = $prod['category'] ?? 'general';
                $imagenUrl = $prod['image'] ?? '';
                $stock = isset($prod['rating']['count']) ? $prod['rating']['count'] : 10;

                if (!$id) continue;

                // Construir jerarquía basada en la categoría de Fake Store
                $subcategory = $this->getOrCreateHierarchy($categoriaRaw);

                // Descargar imagen
                $imagenLocalPath = $this->descargarImagen($imagenUrl, "fs-{$id}");

                // Insertar o actualizar Producto
                $producto = Product::updateOrCreate(
                    ['sku' => "FS-{$id}"],
                    [
                        'name' => $titulo,
                        'price' => $precio,
                        'description' => $descripcion,
                        'image_path' => $imagenLocalPath,
                        'subcategory_id' => $subcategory->id
                    ]
                );

                if ($producto->wasRecentlyCreated) {
                    $contadorNuevos++;
                } else {
                    $contadorActualizados++;
                }

                // Insertar o actualizar Variante por defecto (para el stock)
                Variant::updateOrCreate(
                    ['product_id' => $producto->id, 'sku' => "FS-{$id}"],
                    [
                        'stock' => $stock,
                        'price' => $precio,
                        'image_path' => $imagenLocalPath,
                    ]
                );
            }

            $this->info("✅ ¡Proceso terminado!");
            $this->line("- Insertados: <fg=green>{$contadorNuevos}</>");
            $this->line("- Actualizados: <fg=yellow>{$contadorActualizados}</>");

        } catch (\Exception $e) {
            $this->error("❌ Ocurrió un error inesperado: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Descarga la imagen y la guarda en el storage local.
     */
    private function descargarImagen(string $url, string $sku): string
    {
        if (empty($url)) return null;

        // Extraer la extensión de la URL si es posible, sino asumir jpg
        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        $extension = $extension ? $extension : 'jpg';
        
        $path = "products/{$sku}.{$extension}";

        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

        try {
            $imageContents = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0'
            ])->get($url)->body();
            
            Storage::disk('public')->put($path, $imageContents);
            return $path;
        } catch (\Exception $e) {
            $this->warn("No se pudo descargar la imagen para SKU {$sku}");
            return null;
        }
    }

    /**
     * Crea o busca la familia, categoría y subcategoría respetando el nombre provisto por la API.
     */
    private function getOrCreateHierarchy(string $categoryName)
    {
        $formattedName = Str::title($categoryName);

        // Familia
        $family = Family::firstOrCreate(
            ['name' => $formattedName]
        );

        // Categoría
        $category = Category::firstOrCreate(
            ['name' => $formattedName, 'family_id' => $family->id]
        );

        // Subcategoría
        $subcategory = Subcategory::firstOrCreate(
            ['name' => $formattedName, 'category_id' => $category->id]
        );

        return $subcategory;
    }
}
