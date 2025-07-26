<?php

namespace App\Http\Controllers;

use App\Models\Magazine;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MagazineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Magazine::all();
    }

    public function indexPage(Request $request)
    {
        $filterMangas = function ($request) {
            $page = $request->input('page', 1);
            $search = $request->input('search');

            $query = Magazine::query();

            //Filtro de búsqueda
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            //Filtro de demografías
            if (!empty($request->demographies)) {
                $query->whereIn('demography', $request->demographies);
            }

            //Filtro de fecha
            if (!empty($request->date)) {
                $query->where('date', '<=', $request->date);
            }

            //Filtro de periodicidad
            if (!empty($request->frequencies)) {
                $query->whereIn('frequency', $request->frequencies);
            }

            //Filtro de editorial
            if (!empty($request->publishers)) {
                $query->whereIn('publisher', $request->publishers);
            }

            //Orden
            switch ($request->order) {
                case 'updateDesc':
                    $query->orderBy('updated_at', 'desc');
                    break;
                case 'updateAsc':
                    $query->orderBy('updated_at', 'asc');
                    break;
                case 'nameDesc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'nameAsc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'dateDesc':
                    $query->orderBy('date', 'desc');
                    break;
                case 'dateAsc':
                    $query->orderBy('date', 'asc');
                    break;
                default:
                    $query->orderBy('name', 'asc');
            }

            return $query->paginate(24, page: $page);
        };

        $props['pagination'] = Inertia::defer(fn() => $filterMangas($request))->deepMerge();

        $props['filtersData'] = Inertia::defer(
            fn() => [
                'publishers' => Magazine::distinct()->pluck('publisher')->toArray(),
            ]
        );

        return Inertia::render('magazine/Index', $props);
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

        return to_route('admin.create', ['tab' => 'magazine']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Magazine $magazine)
    {
        return Inertia::render('magazine/Show', ['magazine' => $magazine]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Magazine $magazine)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'publisher' => 'required|string',
            'demography' => ['required', 'string', Rule::in(['shounen', 'shoujo', 'seinen', 'josei'])],
            'frequency' => ['required', 'string', Rule::in(['weekly', 'biweekly', 'monthly', 'bimonthly', 'quarterly', 'irregular'])],
            'date' => 'nullable|date',
        ]);

        $magazine->update($validatedData);

        return to_route('admin.index', ['tab' => 'magazine']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer',
        ]);

        $magazine = Magazine::find($validatedData['id']);

        if (!$magazine) {
            return response()->json(['message' => 'Revista no encontrada'], 404);
        }

        $magazine->delete();

        return to_route('admin.index');
    }
}
