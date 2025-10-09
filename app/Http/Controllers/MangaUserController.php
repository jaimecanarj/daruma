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
            //Si el estado es reading, añadir el campo started_at, y si es completed añadir también el campo completed_at
            if ($validatedData['status'] == 'reading') {
                $validatedData['started_at'] = now();
            } elseif ($validatedData['status'] == 'completed') {
                $validatedData['completed_at'] = now();
            }

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
            //Si el estado es completed actualizar el campo completed_at, si no borrarlo
            if ($validatedData['status'] == 'completed') {
                $validatedData['completed_at'] = now();
            } else {
                $validatedData['completed_at'] = null;
            }

            //Si el estado es wishlist, borrar la fecha de completed_at y started_at
            if ($validatedData['status'] == 'wishlist') {
                $validatedData['started_at'] = null;
            } elseif ($validatedData['status'] == 'reading') {
                $validatedData['started_at'] = now();
            }

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
