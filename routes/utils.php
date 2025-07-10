<?php

use App\Models\Magazine;
use App\Models\Manga;
use App\Models\Person;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('/search', function () {
        $mangas = Manga::select('id', 'name', 'cover')->get();
        $people = Person::all();
        $magazines = Magazine::select('id', 'name', 'publisher')->get();

        return ['mangas' => $mangas, 'people' => $people, 'magazines' => $magazines];
    });
});
