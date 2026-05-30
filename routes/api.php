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

// Chatbot proxy
Route::get('chatbot/models',  [\App\Http\Controllers\ChatbotController::class, 'models']);
Route::post('chatbot/chat',   [\App\Http\Controllers\ChatbotController::class, 'chat']);
Route::post('chatbot/clear-session', [\App\Http\Controllers\ChatbotController::class, 'clearSession']);

// API v1 Portal Administrativo
Route::prefix('v1')->name('api.v1.')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', \App\Http\Controllers\Api\ProductController::class);
});