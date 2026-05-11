<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class ProductController extends Controller
{
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
