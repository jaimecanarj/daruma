<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ChapterController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //Validamos los datos recibidos
        $validatedData = $request->validate([
            'name' => 'required|string',
            'order' => 'required|integer|min:1',
            'volume_order' => 'required|integer',
            'manga_id' => 'required|integer',
        ]);

        try {
            //Almacenamos en la base de datos
            $volume = Chapter::create($validatedData);
        } catch (QueryException $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al guardar el capítulo. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }

    public function show(Chapter $chapter)
    {
        //
    }

    public function update(Request $request)
    {
        //Validamos los datos recibidos
        $validatedData = $request->validate([
            'name' => 'required|string',
            'order' => 'required|integer|min:1',
            'volume_order' => 'required|integer',
            'manga_id' => 'required|integer',
        ]);

        try {
            //Almacenamos en la base de datos
            $volume = Chapter::updateOrCreate(['order' => $validatedData['order'], 'manga_id' => $validatedData['manga_id']], $validatedData);
        } catch (QueryException $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al guardar el capítulo. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }

    public function destroy(Chapter $chapter)
    {
        //
    }
}
