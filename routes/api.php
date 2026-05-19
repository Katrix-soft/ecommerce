<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\SortController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('sort/covers', [SortController::class, 'covers'])->name('api.sort.covers');

// Mercado Pago Checkout Bricks
Route::get('mercadopago/public-key', [\App\Http\Controllers\MercadoPagoController::class, 'getPublicKey']);
Route::post('mercadopago/process-payment', [\App\Http\Controllers\MercadoPagoController::class, 'processPayment'])
    ->middleware('auth:sanctum');

// Webhooks
Route::post('webhooks/mercadopago', [\App\Http\Controllers\MercadoPagoController::class, 'handleWebhook']);