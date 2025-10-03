<?php

namespace App\Http\Controllers;

use App\Models\MangaUser;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MangaUserController extends Controller
{
    //Store
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'manga_id' => 'required|integer|exists:mangas,id',
            'user_id' => 'required|integer|exists:users,id',
            'status' => ['required', 'string', Rule::in(['reading', 'completed', 'on_hold', 'wishlist'])],
            'favorite' => 'boolean|nullable',
        ]);

        try {
            // Almacenar en la base de datos
            return MangaUser::create($validatedData);
        } catch (QueryException $e) {
            // Verificar si es un error de entrada duplicada
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw ValidationException::withMessages([
                    'general' => ['Ya existe seguimiento del manga.'],
                ]);
            }

            throw ValidationException::withMessages([
                'general' => ['Error al añadir seguimiento del manga. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }

    public function update(Request $request)
    {
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'manga_id' => 'required|integer|exists:mangas,id',
            'user_id' => 'required|integer|exists:users,id',
            'status' => ['nullable', 'string', Rule::in(['reading', 'completed', 'on_hold', 'wishlist'])],
            'favorite' => 'boolean|nullable',
        ]);

        try {
            //Actualizar seguimiento
            MangaUser::where([
                'manga_id' => $validatedData['manga_id'],
                'user_id' => $validatedData['user_id'],
            ])->update($validatedData);
        } catch (QueryException $e) {
            // Verificar si es un error de entrada duplicada
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw ValidationException::withMessages([
                    'general' => ['Ya existe seguimiento del manga.'],
                ]);
            }

            throw ValidationException::withMessages([
                'general' => ['Error al actualizar seguimiento del manga. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'manga_id' => 'required|integer|exists:mangas,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);
        try {
            $deleted = MangaUser::where([
                'manga_id' => $validatedData['manga_id'],
                'user_id' => $validatedData['user_id'],
            ])->delete();

            if (!$deleted) {
                throw ValidationException::withMessages([
                    'general' => ['Seguimiento no encontrado.'],
                ]);
            }
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al eliminar el seguimiento. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }
}
