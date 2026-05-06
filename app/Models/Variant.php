<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Variant extends Model
{
    use HasFactory;

    public function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->image_path ? Storage::url($this->image_path) : asset('images/no-image.jpg'),
        );
    }

    protected $fillable = [
        'sku',
        'image_path',
        'product_id'
    ];
    // relacion uno a muchos inversa
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    // relacion muchos a muchos
    public function features()
    {
        return $this->belongsToMany(Feature::class)
                    ->withTimestamps();
    }
}
