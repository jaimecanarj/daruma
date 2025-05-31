<?php

namespace App\Http\Controllers;

use App\Models\Magazine;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MagazineController extends Controller
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
        //Validar los datos recibidos
        $validatedData = $request->validate([
            'name' => 'required|string',
            'publisher' => 'required|string',
            'demography' => ['required', 'string', Rule::in(['shounen', 'shoujo', 'seinen', 'josei'])],
            'frequency' => ['required', 'string', Rule::in(['weekly', 'biweekly', 'monthly', 'bimonthly', 'quarterly', 'irregular'])],
            'date' => 'nullable|date',
        ]);

        //Almacenar en la base de datos
        Magazine::create($validatedData);

        return to_route('admin.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Magazine $magazine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Magazine $magazine)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Magazine $magazine)
    {
        //
    }
}
