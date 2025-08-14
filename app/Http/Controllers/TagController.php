<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }

    public function store(Request $request)
    {
        //Validar los datos recibidos
        $validatedData = $request->validate([
            'name' => 'required|string',
            'type' => ['required', 'string', Rule::in(['genre', 'theme'])],
        ]);

        try {
            //Almacenar en la base de datos
            Tag::create($validatedData);

            return to_route('admin.create', ['tab' => 'tag']);
        } catch (QueryException $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al crear la etiqueta. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }

    public function update(Request $request, Tag $tag)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'type' => ['required', 'string', Rule::in(['genre', 'theme'])],
        ]);

        try {
            $tag->update($validatedData);

            return to_route('admin.index', ['tab' => 'tag']);
        } catch (QueryException $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al crear la etiqueta. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer',
        ]);

        $tag = Tag::find($validatedData['id']);

        if (!$tag) {
            throw ValidationException::withMessages([
                'general' => ['Etiqueta no encontrada.'],
            ]);
        }

        try {
            $tag->delete();

            return to_route('admin.index');
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al eliminar la etiqueta. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }
}
