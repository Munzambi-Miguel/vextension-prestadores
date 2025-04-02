<?php

// Rotas para o módulo Prestadores
Route::prefix('prestadores')->group(function () {
    Route::get('index', [\Prestadores\App\Http\Controllers\PestadoreController::class, 'index'])->name('Prestadores.index');
    Route::post('index', [\Prestadores\App\Http\Controllers\PestadoreController::class, 'store'])->name('Prestadores.store');
})->middleware(['auth', 'verified']);