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
    
    public $sku = '';
    public $name = '';
    public $description = '';
    public $price = '';
    public $subcategory_id = '';

    public $families;
    public $family_id = '';
    public $category_id = '';

    public $image;

    public function mount($product)
    {
        $this->product = $product;
        $this->sku = $product->sku;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->subcategory_id = $product->subcategory_id;

        $this->families = Family::all();
        $this->category_id = $product->subcategory->category->id;
        $this->family_id = $product->subcategory->category->family_id;
    }

    public function updatedFamilyId($value)
    {
        $this->category_id = '';
        $this->subcategory_id = '';
    }

    public function updatedCategoryId($value)
    {
        $this->subcategory_id = '';
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

    public function rules()
    {
        return [
            'image' => 'nullable|image|max:10240',
            'sku' => 'required|unique:products,sku,' . $this->product->id,
            'name' => 'required|min:3|max:100',
            'description' => 'required|max:500',
            'price' => 'required|numeric',
            'family_id' => 'required|exists:families,id',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
        ];
    }

    public function validationAttributes()
    {
        return [
            'image' => 'imagen',
            'sku' => 'código',
            'name' => 'nombre',
            'description' => 'descripción',
            'price' => 'precio',
            'family_id' => 'familia',
            'category_id' => 'categoría',
            'subcategory_id' => 'subcategoría',
        ];
    }

    public function update()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'El formulario contiene errores',
            ]);
            throw $e;
        }

        $data = [
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'subcategory_id' => $this->subcategory_id,
        ];

        if ($this->image) {
            $data['image_path'] = $this->image->store('products', 'public');
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
