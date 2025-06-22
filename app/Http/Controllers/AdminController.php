<?php

namespace App\Http\Controllers;

use App\Models\Magazine;
use App\Models\Manga;
use App\Models\Person;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Index');
    }

    public function create($tab): Response
    {
        $props = [];

        //Si voy a crear un manga, obtengo los datos necesarios
        if ($tab === 'manga') {
            $props['mangas'] = Inertia::defer(fn() => Manga::all()->select('id', 'name'));
            $props['people'] = Inertia::defer(fn() => Person::all()->select('id', 'name', 'surname'));
            $props['magazines'] = Inertia::defer(fn() => Magazine::all()->select('id', 'name'));
            $props['tags'] = Inertia::defer(fn() => Tag::all());
        }

        return Inertia::render('admin/Create', $props);
    }

    public function edit($tab, $id): Response
    {
        $props = [];

        // Mapa de tabs a modelos
        $models = [
            'manga' => Manga::class,
            'person' => Person::class,
            'magazine' => Magazine::class,
            'user' => User::class,
            'tag' => Tag::class,
        ];

        // Verificar si el tab existe en nuestro mapa
        if (isset($models[$tab])) {
            // Cargar el modelo correspondiente
            $props['item'] = Inertia::defer(fn() => $models[$tab]::find($id));

            // Cargar datos adicionales solo para manga
            if ($tab === 'manga') {
                $props['mangas'] = Inertia::defer(fn() => Manga::all()->select('id', 'name'));
                $props['people'] = Inertia::defer(fn() => Person::all()->select('id', 'name', 'surname'));
                $props['magazines'] = Inertia::defer(fn() => Magazine::all()->select('id', 'name'));
                $props['tags'] = Inertia::defer(fn() => Tag::all());
            }
        }

        return Inertia::render('admin/Edit', $props);
    }
}
