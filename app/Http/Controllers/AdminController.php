<?php

namespace App\Http\Controllers;

use App\Models\Magazine;
use App\Models\Manga;
use App\Models\Person;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Index');
    }

    public function create(): Response
    {
        return Inertia::render('admin/Create', [
            'mangas' => Inertia::defer(fn() => Manga::all()->select('id', 'name')),
            'people' => Inertia::defer(fn() => Person::all()->select('id', 'name', 'surname')),
            'magazines' => Inertia::defer(fn() => Magazine::all()->select('id', 'name')),
            'tags' => Inertia::defer(fn() => Tag::all()),
        ]);
    }
}
