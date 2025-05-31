<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'name' => 'required|string',
            'kanjiName' => 'string|nullable',
            'surname' => 'string|nullable',
            'kanjiSurname' => 'string|nullable',
        ]);

        // Almacenar en la base de datos
        Person::create([
            'name' => $validatedData['name'],
            'kanji_name' => $validatedData['kanjiName'],
            'surname' => $validatedData['surname'],
            'kanji_surname' => $validatedData['kanjiSurname'],
        ]);

        return to_route('admin.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Person $person)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person)
    {
        //
    }
}
