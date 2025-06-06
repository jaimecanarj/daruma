<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\MagazineController;
use App\Http\Controllers\MangaController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Rutas de administración
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/create/{tab?}', [AdminController::class, 'create'])->name('admin.create');
    Route::get('/edit/{tab?}/{id}', [AdminController::class, 'create'])->name('admin.edit');
});

// Rutas para almacenar recursos
Route::post('/person/store', [PersonController::class, 'store']);
Route::post('/magazine/store', [MagazineController::class, 'store']);
Route::post('/manga/store', [MangaController::class, 'store']);
Route::post('/store', [UserController::class, 'store'])->name('user.store');

Route::prefix('tag')->group(function () {
    Route::get('/', [TagController::class, 'index'])->name('tag.index');
    Route::post('/store', [TagController::class, 'store'])->name('tag.store');
    Route::post('/update', [TagController::class, 'update'])->name('tag.update');
    Route::post('/delete', [TagController::class, 'destroy'])->name('tag.destroy');
});

require __DIR__ . '/auth.php';
