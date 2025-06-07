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

// Rutas para manejar recursos
Route::prefix('manga')->group(function () {
    Route::get('/', [MangaController::class, 'index'])->name('manga.index');
    Route::post('/store', [MangaController::class, 'store'])->name('manga.store');
    Route::post('/update', [MangaController::class, 'update'])->name('manga.update');
    Route::post('/delete', [MangaController::class, 'destroy'])->name('manga.destroy');
});

Route::prefix('person')->group(function () {
    Route::get('/', [PersonController::class, 'index'])->name('person.index');
    Route::post('/store', [PersonController::class, 'store'])->name('person.store');
    Route::post('/update', [PersonController::class, 'update'])->name('person.update');
    Route::post('/delete', [PersonController::class, 'destroy'])->name('person.destroy');
});

Route::prefix('magazine')->group(function () {
    Route::get('/', [MagazineController::class, 'index'])->name('magazine.index');
    Route::post('/store', [MagazineController::class, 'store'])->name('magazine.store');
    Route::post('/update', [MagazineController::class, 'update'])->name('magazine.update');
    Route::post('/delete', [MagazineController::class, 'destroy'])->name('magazine.destroy');
});

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user.index');
    Route::post('/store', [UserController::class, 'store'])->name('user.store');
    Route::post('/update', [UserController::class, 'update'])->name('user.update');
    Route::post('/delete', [UserController::class, 'destroy'])->name('user.destroy');
});

Route::prefix('tag')->group(function () {
    Route::get('/', [TagController::class, 'index'])->name('tag.index');
    Route::post('/store', [TagController::class, 'store'])->name('tag.store');
    Route::post('/update', [TagController::class, 'update'])->name('tag.update');
    Route::post('/delete', [TagController::class, 'destroy'])->name('tag.destroy');
});

require __DIR__ . '/auth.php';
