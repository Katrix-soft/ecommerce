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
