<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\CoverObserver;

#[ObservedBy([CoverObserver::class])]
class Cover extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'title',
        'start_at',
        'end_at',
        'is_active',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];
    
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn() => asset('storage/' . $this->image_path),
        );
    }
        
        
}
