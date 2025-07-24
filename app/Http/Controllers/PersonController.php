<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
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

            $query = Person::query();

            //Filtro de búsqueda
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            return $query->paginate(24, page: $page);
        };

        $props['pagination'] = Inertia::defer(fn() => $filterMangas($request))->deepMerge();

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

        // Almacenar en la base de datos
        Person::create($validatedData);

        return to_route('admin.create', ['tab' => 'person']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person)
    {
        return Inertia::render('person/Show', ['person' => $person]);
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
