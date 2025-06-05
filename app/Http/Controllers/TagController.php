<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Validar los datos recibidos
        $validatedData = $request->validate([
            'name' => 'required|string',
            'type' => ['required', 'string', Rule::in(['genre', 'theme'])],
        ]);

        //Almacenar en la base de datos
        Tag::create($validatedData);

        return to_route('admin.create');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request) {}
}
