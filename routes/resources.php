<?php

use App\Http\Controllers\MagazineController;
use App\Http\Controllers\MangaController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::prefix('manga')->group(function () {
        Route::get('/', [MangaController::class, 'index'])->name('manga.index');
        Route::post('/store', [MangaController::class, 'store'])->name('manga.store');
        Route::post('/update/{manga}', [MangaController::class, 'update'])->name('manga.update');
        Route::delete('/delete', [MangaController::class, 'destroy'])->name('manga.destroy');
    });
    Route::prefix('person')->group(function () {
        Route::get('/', [PersonController::class, 'index'])->name('person.index');
        Route::post('/store', [PersonController::class, 'store'])->name('person.store');
        Route::put('/update/{person}', [PersonController::class, 'update'])->name('person.update');
        Route::delete('/delete', [PersonController::class, 'destroy'])->name('person.destroy');
    });
    Route::prefix('magazine')->group(function () {
        Route::get('/', [MagazineController::class, 'index'])->name('magazine.index');
        Route::post('/store', [MagazineController::class, 'store'])->name('magazine.store');
        Route::put('/update/{magazine}', [MagazineController::class, 'update'])->name('magazine.update');
        Route::delete('/delete', [MagazineController::class, 'destroy'])->name('magazine.destroy');
    });
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::post('/store', [UserController::class, 'store'])->name('user.store');
        Route::put('/update/{user}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/delete', [UserController::class, 'destroy'])->name('user.destroy');
    });
    Route::prefix('tag')->group(function () {
        Route::get('/', [TagController::class, 'index'])->name('tag.index');
        Route::post('/store', [TagController::class, 'store'])->name('tag.store');
        Route::put('/update/{tag}', [TagController::class, 'update'])->name('tag.update');
        Route::delete('/delete', [TagController::class, 'destroy'])->name('tag.destroy');
    });
});
