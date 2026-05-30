<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id'
    ];
    // relacion uno a muchos inversa
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // relacion uno a muchos
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // relacion muchos a muchos
    public function options()
    {
        return $this->belongsToMany(Option::class, 'option_subcategory')
                    ->withTimestamps();
    }
}
