<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
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
    public function index()
    {
        $products = Product::with('subcategory.category.family')
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('admin.products.index', compact('products'));
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
