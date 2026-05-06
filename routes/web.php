<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Option;

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
//     $array1 = ['a','b'];
//     $array2 = ['a','b'];
//     $array3 = ['a','b'];
//     $arrays = [$array1 , $array2, $array3];

//     $combinaciones = generarCombinaciones($arrays);
// return $combinaciones;
    $features = Product::find(10)->options-pluck('pivot.features');
    $combinaciones = generarCombinaciones($features);
    foreach($combinaciones as $combinacion){
        $variant=
    }
});

function generarCombinaciones($arrays)
{
    $resultado = [[]];
    foreach ($arrays as $array) {
        $nuevaCombinacion = [];
        foreach ($resultado as $combinacion) {
            foreach ($array as $valor) {
                $nuevaCombinacion[] = array_merge($combinacion, [$valor]);
            }
        }
        $resultado = $nuevaCombinacion;
    }
    return $resultado;
}