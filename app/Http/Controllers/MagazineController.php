<?php

namespace App\Http\Controllers;

use App\Models\Magazine;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MagazineController extends Controller
{
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

        $props['paginatedResults'] = Inertia::defer(fn() => $filterMangas($request))->deepMerge();

        $props['filterOptions'] = Inertia::defer(
            fn() => [
                'publishers' => Magazine::distinct()->pluck('publisher')->toArray(),
            ]
        );

        return Inertia::render('magazine/Index', $props);
    }

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

        try {
            //Almacenar en la base de datos
            Magazine::create($validatedData);

            return to_route('admin.create', ['tab' => 'magazine']);
        } catch (QueryException $e) {
            // Verificar si es un error de entrada duplicada
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw ValidationException::withMessages([
                    'general' => ['Esta revista ya existe en la base de datos.'],
                ]);
            }

            throw ValidationException::withMessages([
                'general' => ['Error al crear la revista. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }

    public function show($id)
    {
        return Inertia::render('magazine/Show', ['magazine' => Inertia::defer(fn() => Magazine::find($id)->load('mangas'))]);
    }

    public function update(Request $request, Magazine $magazine)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'publisher' => 'required|string',
            'demography' => ['required', 'string', Rule::in(['shounen', 'shoujo', 'seinen', 'josei'])],
            'frequency' => ['required', 'string', Rule::in(['weekly', 'biweekly', 'monthly', 'bimonthly', 'quarterly', 'irregular'])],
            'date' => 'nullable|date',
        ]);

        try {
            $magazine->update($validatedData);

            return to_route('admin.index', ['tab' => 'magazine']);
        } catch (QueryException $e) {
            // Verificar si es un error de entrada duplicada
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw ValidationException::withMessages([
                    'general' => ['Esta revista ya existe en la base de datos.'],
                ]);
            }

            throw ValidationException::withMessages([
                'general' => ['Error al actualizar la revista. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer',
        ]);

        $magazine = Magazine::find($validatedData['id']);

        if (!$magazine) {
            throw ValidationException::withMessages([
                'general' => ['Revista no encontrada.'],
            ]);
        }

        try {
            $magazine->delete();

            return to_route('admin.index');
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al eliminar la revista. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }
}
