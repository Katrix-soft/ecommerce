<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Family;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Livewire\WithFileUploads;

class ProductCreate extends Component
{

    use WithFileUploads;

    public $families;
    public $family_id = '';
    public $category_id = '';


    public $sku = '';
    public $name = '';
    public $description = '';
    public $price = '';

    public $image;

    public function mount()
    {
        $this->families = Family::all();
    }

    public function updatedFamilyId($value)
    {
        $this->category_id = '';
        $this->product['subcategory_id'] = '';
    }

    public function updatedCategoryId($value)
    {
        $this->product['subcategory_id'] = '';
    }

    #[Computed]
    public function categories()
    {
        return Category::where('family_id', $this->family_id)->get();
    }

    #[Computed]
    public function subcategories()
    {
        return Subcategory::where('category_id', $this->category_id)->get();
    }


    public function store()
    {
        try {
            $this->validate([
                'image' => 'required|image|max:10240',
                'sku' => 'required|unique:products,sku',
                'name' => 'required|min:3|max:100',
                'description' => 'required|max:200',
                'price' => 'required|numeric',
                'family_id' => 'required|exists:families,id',
                'category_id' => 'required|exists:categories,id',
                'subcategory_id' => 'required|exists:subcategories,id',
            ], [], [
                'image' => 'imagen',
                'sku' => 'código',
                'name' => 'nombre',
                'description' => 'descripción',
                'price' => 'precio',
                'family_id' => 'familia',
                'category_id' => 'categoría',
                'subcategory_id' => 'subcategoría',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'El formulario contiene errores',
            ]);
            throw $e;
        }

        $imagePath = $this->image->store('products', 'public');

        $product = Product::create([
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'subcategory_id' => $this->subcategory_id,
            'image_path' => $imagePath,
        ]);

        $this->reset([
            'sku', 'name', 'description', 'price', 'image',
            'family_id', 'category_id', 'subcategory_id',
        ]);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Bien hecho',
            'text' => 'Producto creado correctamente',
        ]);

        return redirect()->route('admin.products.edit', $product);
    }

    public function render()
    {
        return view('livewire.admin.products.product-create');
    }
}
