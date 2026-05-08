<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FamilyController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\CoverController;



Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

Route::get('options', [OptionController::class, 'index'])->name('options.index');

Route::resource('families', FamilyController::class);

Route::resource('categories',CategoryController::Class);

Route::resource('subcategories',SubcategoryController::Class);

Route::resource('products',ProductController::Class);

Route::post('products/{product}/variants', [ProductController::class, 'Variants'])->name('products.variants')

  ->scopeBindings();
Route::resource('covers', CoverController::class);