<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Option;
use App\Models\Variant;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
Route::get('prueba', function(){

    $product->variants()->delete();

    $product = Product::find(10);
    $features = $product->options-pluck('pivot.features');
    $combinaciones = generarCombinaciones($features);
    foreach($combinaciones as $combinacion){
        $variant=Variant::create([
            'product_id'=>$product->id,
        ]);
        $variant->features()->attach($combinacion);
    }
});

