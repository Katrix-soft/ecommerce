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

// Rutas de administración de órdenes, conductores y envíos (Logística)
Route::get('orders', \App\Livewire\Admin\OrdersIndex::class)->name('orders.index');
Route::get('orders/{order}/print', [\App\Http\Controllers\Admin\OrderController::class, 'print'])->name('orders.print');
Route::get('drivers', \App\Livewire\Admin\DriversIndex::class)->name('drivers.index');
Route::get('shipments', \App\Livewire\Admin\ShipmentsIndex::class)->name('shipments.index');