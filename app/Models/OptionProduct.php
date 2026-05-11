<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;



class OptionProduct extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'option_id',
        'product_id',
        'feature_id'
    ];

    protected $casts = [
        'feature_id' => 'array'
    ];


}
