<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'avatar' => 'nullable|file|mimes:jpg,jpeg,png,jxl',
            'password' => ['required', 'min:8', 'confirmed', Rules\Password::defaults()],
        ]);

        //Compruebo que se ha pasado un avatar
        if ($request->hasFile('avatar') && $request->file('avatar')->getSize() > 0) {
            // Crear un nombre de archivo único basado en timestamp y un string aleatorio
            $filename = time() . '-' . Str::random(10) . '.' . $validatedData['avatar']->getClientOriginalExtension();
            $path = 'avatars/' . $filename;

            // Crear la carpeta si no existe
            $directory = storage_path('app/public/avatars');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Guardar avatar
            Storage::disk('public')->putFileAs('avatars', $validatedData['avatar'], $filename);

            // Reemplazar con la ruta del archivo
            $validatedData['avatar'] = $path;
        }

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        $user->assignRole('user');

        event(new Registered($user));

        Auth::login($user);

        return to_route('home');
    }
}
