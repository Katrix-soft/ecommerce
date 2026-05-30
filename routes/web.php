<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Option;
use App\Models\Variant;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShippingController;
Route::get('/', [WelcomeController::class, 'index'])->name('welcome.index');

// Chatbot proxy con CSRF y rate limiting
Route::get('chatbot/models', [\App\Http\Controllers\ChatbotController::class, 'models'])
    ->middleware('throttle:15,1');
Route::post('chatbot/chat', [\App\Http\Controllers\ChatbotController::class, 'chat'])
    ->middleware('throttle:5,1');
Route::post('chatbot/clear-session', [\App\Http\Controllers\ChatbotController::class, 'clearSession'])
    ->middleware('throttle:10,1');


Route::get('families/{family}', [FamilyController::class, 'show'])->name('families.show');

Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('subcategories/{subcategory}', [SubcategoryController::class, 'show'])->name('subcategories.show');

Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('cart', [CartController::class, 'index'])->name('cart.index');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->hasRole('superadmin')) {
            return redirect()->route('superadmin.dashboard');
        } elseif (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('welcome.index');
    })->name('dashboard');

    Route::get('shipping', [ShippingController::class, 'index'])->name('shipping.index');
    Route::get('shipping/create', [ShippingController::class, 'create'])->name('shipping.create');
    Route::get('checkout', [ShippingController::class, 'index'])->name('checkout');
    
    // Mercado Pago Process Payment (using web session)
    Route::post('mercadopago/process-payment', [\App\Http\Controllers\MercadoPagoController::class, 'processPayment'])->name('mercadopago.process');
});

