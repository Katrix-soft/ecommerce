<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Family;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Livewire\WithFileUploads;

class ProductEdit extends Component
{
    use WithFileUploads;

    public $product;
    public $productEdit;

    public $families;
    public $family_id = '';
    public $category_id = '';

    public $image;

    public function mount($product)
    {
        $this->productEdit = $product->only('sku','name','description','image_path','price','subcategory_id');
        $this->families = Family::all();
        $this->category_id = $product->subcategory->category->id;
        $this->family_id = $product->subcategory->category->family_id;
    }

    public function updatedFamilyId($value)
    {
        $this->category_id = '';
        $this->productEdit['subcategory_id'] = '';
    }

    public function updatedCategoryId($value)
    {
        $this->productEdit['subcategory_id'] = '';
    }

    #[Computed()]
    public function categories()
    {
        return Category::where('family_id', $this->family_id)->get();
    }

    #[Computed()]
    public function subcategories()
    {
        return Subcategory::where('category_id', $this->category_id)->get();
    }

    public function update()
    {
        try {
            $this->validate([
                'image' => 'nullable|image|max:10240',
                'productEdit.sku' => 'required|unique:products,sku,' . $this->product->id,
                'productEdit.name' => 'required|min:3|max:100',
                'productEdit.description' => 'required|max:200',
                'productEdit.price' => 'required|numeric',
                'family_id' => 'required|exists:families,id',
                'category_id' => 'required|exists:categories,id',
                'productEdit.subcategory_id' => 'required|exists:subcategories,id',
            ], [], [
                'image' => 'imagen',
                'productEdit.sku' => 'código',
                'productEdit.name' => 'nombre',
                'productEdit.description' => 'descripción',
                'productEdit.price' => 'precio',
                'family_id' => 'familia',
                'category_id' => 'categoría',
                'productEdit.subcategory_id' => 'subcategoría',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'El formulario contiene errores',
            ]);
            throw $e;
        }

        $data = $this->productEdit;

        if ($this->image) {
            $data['image_path'] = $this->image->store('products');
        }

        $this->product->update($data);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Bien hecho',
            'text' => 'Producto actualizado correctamente',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.products.product-edit');
    }
}
