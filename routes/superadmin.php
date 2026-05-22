<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas del Super Admin
|--------------------------------------------------------------------------
| Protegidas por middleware 'superadmin'. Solo accesibles por usuarios
| con rol superadmin (katrixdevs@gmail.com).
|
*/

Route::get('/', function () {
    return redirect()->route('superadmin.modules');
})->name('dashboard');

Route::get('modules', \App\Livewire\SuperAdmin\ModulesManager::class)->name('modules');
