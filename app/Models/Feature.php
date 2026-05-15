<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'description',
        'option_id'
    ];
    
    // relacion uno a muchos inversa
    public function option()
    {
        return $this->belongsTo(Option::class);
    }
    // relacion muchos a muchos
    public function variants()
    {
        return $this->belongsToMany(Variant::class)
                    ->withTimestamps();
    }
    public function scopeVerifyFamily($query, $family_id)
    {
        return $query->whereHas('variants.product.subcategory.category', function ($query) use ($family_id) {
            $query->where('family_id', $family_id);
        });
    }

    public function scopeVerifyCategory($query, $category_id)
    {
        return $query->whereHas('variants.product.subcategory', function ($query) use ($category_id) {
            $query->where('category_id', $category_id);
        });
    }

    public function scopeVerifySubcategory($query, $subcategory_id)
    {
        return $query->whereHas('variants.product', function ($query) use ($subcategory_id) {
            $query->where('subcategory_id', $subcategory_id);
        });
    }
}
