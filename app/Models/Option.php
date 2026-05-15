<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type'
    ];

    public function scopeVerifyFamily($query, $family_id)
    {
        return $query->whereHas('products.subcategory.category', function ($query) use ($family_id) {
            $query->where('family_id', $family_id);
        });
    }

    public function scopeVerifyCategory($query, $category_id)
    {
        return $query->whereHas('products.subcategory', function ($query) use ($category_id) {
            $query->where('category_id', $category_id);
        });
    }

    public function scopeVerifySubcategory($query, $subcategory_id)
    {
        return $query->whereHas('products', function ($query) use ($subcategory_id) {
            $query->where('subcategory_id', $subcategory_id);
        });
    }
    // relacion muchos a muchos
    public function products()
    {
        return $this->belongsToMany(Product::class)
                    ->using(OptionProduct::class)
                    ->withPivot('feature_id')
                    ->withTimestamps();
    }

    // relacion uno a muchos
    public function features()
    {
        return $this->hasMany(Feature::class);
    }
}
