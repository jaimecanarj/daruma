<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\MagazineController;
use App\Http\Controllers\MangaController;
use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Rutas de administración
Route::prefix('admin')->group(function () {
    Route::get('/create', [AdminController::class, 'create'])->name('admin.create');
});

// Rutas para almacenar recursos
Route::post('/person/store', [PersonController::class, 'store']);
Route::post('/magazine/store', [MagazineController::class, 'store']);
Route::post('/manga/store', [MangaController::class, 'store']);

require __DIR__ . '/auth.php';
