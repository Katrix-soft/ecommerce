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

// ── CATÁLOGO ──
Route::middleware('check.module:options')->group(function () {
    Route::get('options', [OptionController::class, 'index'])->name('options.index');
});

Route::middleware('check.module:families')->group(function () {
    Route::resource('families', FamilyController::class);
});

Route::middleware('check.module:categories')->group(function () {
    Route::resource('categories', CategoryController::Class);
});

Route::middleware('check.module:subcategories')->group(function () {
    Route::resource('subcategories', SubcategoryController::Class);
});

Route::middleware('check.module:products')->group(function () {
    Route::get('products/search-suggestions', [ProductController::class, 'suggestions'])->name('products.suggestions');
    Route::post('products/parse-document', [ProductController::class, 'parseDocument'])->name('products.parse_document')->middleware('check.module:ai_import');
    Route::resource('products', ProductController::Class);
    Route::post('products/{product}/variants', [ProductController::class, 'Variants'])->name('products.variants')
        ->scopeBindings();
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
});

Route::middleware('check.module:covers')->group(function () {
    Route::resource('covers', CoverController::class);
});

// ── OPERACIONES ──
Route::middleware('check.module:orders')->group(function () {
    Route::get('orders', \App\Livewire\Admin\OrdersIndex::class)->name('orders.index');
    Route::get('orders/{order}/print', [\App\Http\Controllers\Admin\OrderController::class, 'print'])->name('orders.print');
});

Route::middleware('check.module:drivers')->group(function () {
    Route::get('drivers', \App\Livewire\Admin\DriversIndex::class)->name('drivers.index');
});

Route::middleware('check.module:shipments')->group(function () {
    Route::get('shipments', \App\Livewire\Admin\ShipmentsIndex::class)->name('shipments.index');
});

// ── CONFIGURACIÓN ──
Route::middleware('check.module:users')->group(function () {
    Route::get('users', \App\Livewire\Admin\UsersIndex::class)->name('users.index');
});