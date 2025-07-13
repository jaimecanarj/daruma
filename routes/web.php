<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\MangaController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\MagazineController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Index');
})->name('home');

// Rutas de administración
Route::prefix('admin')->group(function () {
    Route::get('/{tab?}', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/create/{tab}', [AdminController::class, 'create'])->name('admin.create');
    Route::get('/edit/{tab}/{id}', [AdminController::class, 'edit'])->name('admin.edit');
});

// Rutas de mangas
Route::prefix('mangas')->group(function () {
    Route::get('/', [MangaController::class, 'indexPage'])->name('mangas.index');
    Route::get('/{manga}', [MangaController::class, 'show'])->name('mangas.show');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/resources.php';
require __DIR__ . '/utils.php';
