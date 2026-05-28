<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage; 
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ProductController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'document' => 'required|file',
        ]);

        $file = $request->file('document');
        $path = $file->store('imports', 'local');
        $fullPath = storage_path('app/' . $path);

        $scriptPath = base_path('import_productos.py');

        // Ejecutar el script usando Python
        $process = new Process(['python', $scriptPath, $fullPath]);
        $process->setTimeout(120);
        $process->run();

        if (!$process->isSuccessful()) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error al ejecutar Python',
                'text' => 'Asegúrate de que Python esté instalado y en el PATH. Detalles: ' . $process->getErrorOutput(),
            ]);
            return redirect()->back();
        }

        $output = $process->getOutput();
        $data = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Respuesta inválida',
                'text' => 'El script no devolvió un JSON válido. Salida: ' . substr($output, 0, 100),
            ]);
            return redirect()->back();
        }

        if (isset($data['error'])) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error en el script',
                'text' => $data['error'],
            ]);
            return redirect()->back();
        }

        if (isset($data['productos'])) {
            $subcategoryId = \App\Models\Subcategory::first()->id ?? 1;

            foreach ($data['productos'] as $prodData) {
                Product::updateOrCreate(
                    ['sku' => $prodData['sku']],
                    [
                        'name' => $prodData['nombre'],
                        'price' => $prodData['precio'],
                        'subcategory_id' => $subcategoryId,
                    ]
                );
            }

            session()->flash('swal', [
                'icon' => 'success',
                'title' => '¡Importación exitosa!',
                'text' => 'Se han importado o actualizado los productos correctamente.',
            ]);
        } else {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Formato incorrecto',
                'text' => 'El script no devolvió la estructura de productos esperada.',
            ]);
        }

        return redirect()->back();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $products = Product::with('subcategory.category.family')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'search'));
    }

    public function suggestions(Request $request)
    {
        $search = $request->input('q');
        if (empty($search)) {
            return response()->json([]);
        }

        $products = Product::where('name', 'like', "%{$search}%")
            ->orWhere('sku', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'sku']);

        return response()->json($products);
    }

    public function parseDocument(Request $request)
    {
        // Aumentamos el tiempo máximo de ejecución de PHP para esta petición (5 minutos)
        set_time_limit(300);

        // 1. Validamos que se haya subido un archivo
        $request->validate([
            'documento' => 'required|file|max:10240', // Max 10MB, ajusta según necesites
        ]);

        $archivo = $request->file('documento');

        // Extraemos las líneas del archivo
        $rows = file($archivo->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        $chunks = array_chunk($rows, 25); // de a 25 filas
        
        $todosLosProductos = [];
        $rawResponses = "";

        try {
            foreach ($chunks as $chunk) {
                // Hacemos el prompt más restrictivo
                $prompt = "Convierte los siguientes productos a un array JSON estrictamente válido. No incluyas explicaciones, resúmenes ni texto adicional. Solo el array JSON con la estructura indicada.\n" .
                          "[{\"nombre\": \"string\", \"precio\": 0, \"descripcion\": \"string\", \"stock\": 0}]\n\n" .
                          "DATOS:\n" . implode("\n", $chunk);

                // Hacemos la petición a OpenWebUI
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENWEBUI_TOKEN'),
                ])->timeout(300)->post('https://vps-katrix-openwebui.juidi9.easypanel.host/api/chat/completions', [
                    'model' => 'qwen2.5:1.5b',
                    'messages' => [['role' => 'user', 'content' => $prompt]]
                ]);

                $jsonString = $response->json('choices.0.message.content');
                
                if (!$jsonString) {
                    $jsonString = $response->body(); // Fallback si no hay clave
                }

                // Extraer estrictamente lo que haya entre corchetes [ ] (el array) 
                // por si el modelo sigue incluyendo texto basura antes o después
                if (is_string($jsonString)) {
                    preg_match('/\[.*\]/s', $jsonString, $matches);
                    if (!empty($matches)) {
                        $jsonString = $matches[0];
                    }
                }

                $data = json_decode((string)$jsonString, true);

                // A veces puede devolver {"productos": [...]} en lugar del array directo
                if (is_array($data)) {
                    if (isset($data['productos'])) {
                        $todosLosProductos = array_merge($todosLosProductos, $data['productos']);
                    } else {
                        // Si no es un array indexado o viene en otro formato, intentamos extraer los items
                        if (array_keys($data) !== range(0, count($data) - 1)) {
                            // Es un objeto (array asociativo), tomamos el primer valor que sea array
                            foreach ($data as $value) {
                                if (is_array($value)) {
                                    $todosLosProductos = array_merge($todosLosProductos, $value);
                                    break;
                                }
                            }
                        } else {
                            $todosLosProductos = array_merge($todosLosProductos, $data);
                        }
                    }
                } else {
                    $rawResponses .= $jsonString . "\n\n";
                }
            }

            // Preparamos el resultado para mostrar en el frontend y guardamos en BD
            if (count($todosLosProductos) > 0) {
                $subcategoryId = \App\Models\Subcategory::first()->id ?? 1;

                foreach ($todosLosProductos as $product) {
                    $sku = $product['sku'] ?? 'SKU-' . strtoupper(uniqid());
                    $name = $product['nombre'] ?? 'Producto sin nombre';
                    $price = $product['precio'] ?? 0;
                    $stock = $product['stock'] ?? 0;

                    \App\Models\Product::updateOrCreate(
                        ['sku' => $sku],
                        [
                            'name' => $name,
                            'price' => $price,
                            'stock' => $stock,
                            'subcategory_id' => $subcategoryId,
                        ]
                    );
                }

                $resultadoFinal = json_encode($todosLosProductos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                $mensaje = count($todosLosProductos) . " productos importados/actualizados correctamente.";
            } else {
                $resultadoFinal = "No se pudo parsear como JSON. Respuesta cruda:\n" . $rawResponses;
                $mensaje = "Error al parsear datos.";
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'resultado' => $resultadoFinal,
                'count' => count($todosLosProductos)
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {

        Storage::disk('public')->delete($product->image_path);

        $product->delete();

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Bien hecho',
            'text' => 'Producto eliminado correctamente',
        ]);
        return redirect()->route('admin.products.index');
    }

    public function Variants(Request $request, Product $product)
    {
        $data = $request->validate([
            'variants.*.sku' => 'nullable|string',
            'variants.*.price' => 'nullable|numeric',
            'variants.*.stock' => 'nullable|integer',
        ]);

        foreach ($request->variants as $variantId => $variantData) {
            $variant = $product->variants()->find($variantId);
            if ($variant) {
                $variant->update($variantData);
            }
        }

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Guardado!',
            'text' => 'Las variantes se han actualizado correctamente',
        ]);

        return redirect()->route('admin.products.edit', $product);
    }
}
