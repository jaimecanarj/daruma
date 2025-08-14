<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        //Validar los datos recibidos
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'avatar' => 'nullable|file|mimes:jpg,jpeg,png,jxl',
            'password' => ['required', 'min:8', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            //Compruebo que se ha pasado un avatar
            if ($request->hasFile('avatar') && $request->file('avatar')->getSize() > 0) {
                // Crear un nombre de archivo único basado en timestamp y un string aleatorio
                $filename = time() . '-' . Str::random(10) . '.' . $validatedData['avatar']->getClientOriginalExtension();
                $path = 'avatars/' . $filename;

                //Determinar si es local o producción
                $isLocalEnvironment = app()->environment('local');

                // Definir la ruta base según el entorno
                if ($isLocalEnvironment) {
                    $directory = storage_path('app/public/avatars');
                } else {
                    $directory = public_path('storage/avatars');
                }

                // Crear la carpeta si no existe
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Guardar avatar
                Storage::disk('public')->putFileAs('avatars', $validatedData['avatar'], $filename);

                // Reemplazar con la ruta del archivo
                $validatedData['avatar'] = $path;
            }

            //Almacenar en la base de datos
            $user = User::create($validatedData);

            $user->assignRole('user');

            return to_route('admin.create', ['tab' => 'user']);
        } catch (QueryException $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al guardar el usuario. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|file|mimes:jpg,jpeg,png,jxl',
        ]);

        try {
            //Compruebo que se ha pasado un nuevo avatar
            if ($request->hasFile('avatar') && $request->file('avatar')->getSize() > 0) {
                //Borro el cover actual
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Crear un nombre de archivo único basado en timestamp y un string aleatorio
                $filename = time() . '-' . Str::random(10) . '.' . $validatedData['avatar']->getClientOriginalExtension();
                $path = 'avatars/' . $filename;

                // Guardar avatar
                Storage::disk('public')->putFileAs('avatars', $validatedData['avatar'], $filename);

                // Reemplazar con la ruta del archivo
                $validatedData['avatar'] = $path;
            } else {
                $validatedData['avatar'] = $user->avatar;
            }

            $user->update($validatedData);

            return to_route('admin.index', ['tab' => 'user']);
        } catch (QueryException $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al actualizar el usuario. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer',
        ]);

        $user = User::find($validatedData['id']);

        if (!$user) {
            throw ValidationException::withMessages([
                'general' => ['Usuario no encontrado.'],
            ]);
        }

        try {
            $user->delete();

            return to_route('admin.index');
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al eliminar el usuario. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }
}
