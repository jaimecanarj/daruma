<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Person::all();
    }

    public function indexPage(Request $request)
    {
        $filterMangas = function ($request) {
            $page = $request->input('page', 1);
            $search = $request->input('search');

            //Obtengo el total de mangas escritos/dibujados
            $query = Person::query()->withCount([
                'mangas as writer_count' => function ($query) {
                    $query->where(function ($q) {
                        $q->where('manga_person.job', 'writer')->orWhere('manga_person.job', 'both');
                    });
                },
                'mangas as illustrator_count' => function ($query) {
                    $query->where(function ($q) {
                        $q->where('manga_person.job', 'illustrator')->orWhere('manga_person.job', 'both');
                    });
                },
            ]);

            //Filtro de búsqueda
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            return $query->paginate(24, page: $page);
        };

        $props['paginatedResults'] = Inertia::defer(fn() => $filterMangas($request))->deepMerge();

        return Inertia::render('person/Index', $props);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'name' => 'required|string',
            'kanji_name' => 'string|nullable',
            'surname' => 'string|nullable',
            'kanji_surname' => 'string|nullable',
        ]);

        try {
            // Almacenar en la base de datos
            Person::create($validatedData);

            return to_route('admin.create', ['tab' => 'person']);
        } catch (QueryException $e) {
            // Verificar si es un error de entrada duplicada
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw ValidationException::withMessages([
                    'general' => ['Esta persona ya existe en la base de datos.'],
                ]);
            }

            // Para otros errores de base de datos, relanzar la excepción
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return Inertia::render('person/Show', ['person' => Inertia::defer(fn() => Person::find($id)->load('mangas'))]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Person $person)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'kanji_name' => 'string|nullable',
            'surname' => 'string|nullable',
            'kanji_surname' => 'string|nullable',
        ]);

        $person->update($validatedData);

        return to_route('admin.index', ['tab' => 'person']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer',
        ]);

        $person = Person::find($validatedData['id']);

        if (!$person) {
            return response()->json(['message' => 'Persona no encontrada'], 404);
        }

        $person->delete();

        return to_route('admin.index');
    }
}
