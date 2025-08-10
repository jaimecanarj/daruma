<?php

namespace App\Http\Controllers;

use App\Models\Magazine;
use App\Models\Manga;
use App\Models\Person;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Inertia\Inertia;

class MangaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Manga::all();
    }

    public function indexPage(Request $request)
    {
        $props = [];

        $filterMangas = function ($request) {
            $page = $request->input('page', 1);
            $search = $request->input('search');

            $query = Manga::with('tags', 'people', 'magazine');

            //Filtro de búsqueda
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')->orWhereHas('names', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', '%' . $search . '%');
                    });
                });
            }

            //Filtro de tomos
            if (!empty($request->volumes)) {
                $query->where('volumes', $request->volumes);
            }

            //Filtro de fecha
            if (!empty($request->date)) {
                $query->where(function ($q) use ($request) {
                    $q->where(function ($subq) use ($request) {
                        $subq->where('start_date', '<=', $request->date)->where(function ($innerq) use ($request) {
                            $innerq->where('end_date', '>=', $request->date)->orWhereNull('end_date');
                        });
                    });
                });
            }

            //Filtro de autores
            if (!empty($request->people)) {
                $query->whereHas('people', function ($q) use ($request) {
                    $q->whereIn('id', $request->people);
                });
            }

            //Filtro de idioma
            if (!empty($request->language)) {
                $query->whereIn('language', $request->language);
            }

            //Filtro de revistas
            if (!empty($request->magazines)) {
                $query->whereIn('magazine_id', $request->magazines);
            }

            //Filtro de demografías
            if (!empty($request->demographies)) {
                $query->whereHas('magazine', function ($q) use ($request) {
                    $q->whereIn('demography', $request->demographies);
                });
            }

            //Filtro de estado
            if (!empty($request->finished)) {
                // Convertir los valores string 'true'/'false' a valores booleanos
                $booleanValues = array_map(function ($value) {
                    if ($value === 'true') {
                        return true;
                    }
                    if ($value === 'false') {
                        return false;
                    }
                    return $value;
                }, $request->finished);

                $query->whereIn('finished', $booleanValues);
            }

            //Filtro de dirección de lectura
            if (!empty($request->reading_direction)) {
                $query->whereIn('reading_direction', $request->reading_direction);
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
                    $query->orderBy('start_date', 'desc');
                    break;
                case 'dateAsc':
                    $query->orderBy('start_date', 'asc');
                    break;
                case 'volumesDesc':
                    $query->orderBy('tankoubon', 'desc');
                    break;
                case 'volumesAsc':
                    $query->orderBy('tankoubon', 'asc');
                    break;
                default:
                    $query->orderBy('updated_at', 'desc');
            }

            return $query->paginate(24, page: $page);
        };

        $props['paginatedResults'] = Inertia::defer(fn() => $filterMangas($request))->deepMerge();

        $props['filterOptions'] = Inertia::defer(
            fn() => [
                'people' => Person::all()->select('id', 'name', 'surname'),
                'magazines' => Magazine::all()->select('id', 'name', 'demography'),
                'tags' => Tag::all()->select('id', 'name', 'type'),
            ]
        );

        return Inertia::render('manga/Index', $props);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Validamos los datos recibidos
        $validatedData = $request->validate([
            'cover' => 'required|file|mimes:jpg,jpeg,png,jxl',
            'name' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:startDate',
            'volumes' => 'nullable|integer|min:1',
            'tankoubon' => 'nullable|integer|min:1',
            'chapters' => 'nullable|integer|min:1',
            'sinopsis' => 'nullable|string',
            'reading_direction' => ['required', 'string', Rule::in(['ltr', 'rtl'])],
            'language' => ['required', 'string', Rule::in(['es', 'en', 'jp'])],
            'finished' => 'nullable|boolean',
            'magazine_id' => 'nullable|integer',
            'mal' => 'nullable|integer',
            'listado_manga' => 'nullable|integer',
            'alternative_names' => 'nullable|array',
            'alternative_names.*.label' => 'required_with:alternative_names|string',
            'alternative_names.*.category' => ['required_with:alternative_names|string', Rule::in(['japanese', 'spanish', 'other'])],
            'authors' => 'nullable|array',
            'authors.*.value' => 'required_with:authors|integer',
            'authors.*.category' => ['required_with:authors', 'string', Rule::in(['writer', 'illustrator', 'both'])],
            'tags' => 'nullable|array',
            'tags.*.value' => 'required_with:tags|integer',
            'related_mangas' => 'nullable|array',
            'related_mangas.*.value' => 'required_with:related_mangas|integer',
            'related_mangas.*.category' => ['required_with:related_mangas', 'string', Rule::in(['prequel', 'sequel', 'main story', 'spin-off'])],
        ]);

        if ($request->hasFile('cover')) {
            // Procesar y optimizar la imagen
            $path = $this->processImage($request->file('cover'));

            // Reemplazar con la ruta del archivo optimizado
            $validatedData['cover'] = $path;
        }

        //Almacenamos en la base de datos
        $manga = Manga::create($validatedData);

        if (isset($validatedData['alternative_names'])) {
            foreach ($validatedData['alternative_names'] as $alternativeName) {
                $manga->names()->create([
                    'name' => $alternativeName['label'],
                    'type' => $alternativeName['category'],
                ]);
            }
        }

        if (isset($validatedData['authors'])) {
            foreach ($validatedData['authors'] as $author) {
                $manga->people()->attach($author['value'], ['job' => $author['category']]);
            }
        }

        if (isset($validatedData['tags'])) {
            foreach ($validatedData['tags'] as $tag) {
                $manga->tags()->attach($tag['value']);
            }
        }

        if (isset($validatedData['related_mangas'])) {
            foreach ($validatedData['related_mangas'] as $relatedManga) {
                $manga->mangasRelated()->attach($relatedManga['value'], ['relation' => $relatedManga['category']]);
            }
        }

        return to_route('admin.create', ['tab' => 'manga']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return Inertia::render('manga/Show', [
            'manga' => Inertia::defer(fn() => Manga::find($id)->load('tags', 'people', 'magazine', 'names', 'mangasRelated')),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Manga $manga)
    {
        $validatedData = $request->validate([
            'cover' => 'nullable|file|mimes:jpg,jpeg,png,jxl',
            'name' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:startDate',
            'volumes' => 'nullable|integer|min:1',
            'tankoubon' => 'nullable|integer|min:1',
            'chapters' => 'nullable|integer|min:1',
            'sinopsis' => 'nullable|string',
            'reading_direction' => ['required', 'string', Rule::in(['ltr', 'rtl'])],
            'language' => ['required', 'string', Rule::in(['es', 'en', 'jp'])],
            'finished' => 'nullable|boolean',
            'magazine_id' => 'nullable|integer',
            'mal' => 'nullable|integer',
            'listado_manga' => 'nullable|integer',
            'alternative_names' => 'nullable|array',
            'alternative_names.*.label' => 'required_with:alternative_names|string',
            'alternative_names.*.category' => ['required_with:alternative_names|string', Rule::in(['japanese', 'spanish', 'other'])],
            'authors' => 'nullable|array',
            'authors.*.value' => 'required_with:authors|integer',
            'authors.*.category' => ['required_with:authors', 'string', Rule::in(['writer', 'illustrator', 'both'])],
            'tags' => 'nullable|array',
            'tags.*.value' => 'required_with:tags|integer',
            'related_mangas' => 'nullable|array',
            'related_mangas.*.value' => 'required_with:related_mangas|integer',
            'related_mangas.*.category' => ['required_with:related_mangas', 'string', Rule::in(['prequel', 'sequel', 'main story', 'spin-off'])],
        ]);

        //Compruebo que se ha pasado una nueva cover
        if ($request->hasFile('cover') && $request->file('cover')->getSize() > 0) {
            //Borro el cover actual
            if (Storage::disk('public')->exists($manga->cover)) {
                Storage::disk('public')->delete($manga->cover);
            }

            // Procesar y optimizar la imagen
            $path = $this->processImage($request->file('cover'));

            // Reemplazar con la ruta del archivo optimizado
            $validatedData['cover'] = $path;
        } else {
            $validatedData['cover'] = $manga->cover;
        }

        $manga->update($validatedData);

        // Actualizar relaciones
        $this->syncAlternativeNames($manga, $validatedData['alternative_names'] ?? null);
        $this->syncRelation($manga, 'people', $validatedData['authors'] ?? null, 'job');
        $this->syncRelation($manga, 'tags', $validatedData['tags'] ?? null);
        $this->syncRelation($manga, 'mangasRelated', $validatedData['related_mangas'] ?? null, 'relation');

        return to_route('admin.index', ['tab' => 'manga']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer',
        ]);

        $manga = Manga::find($validatedData['id']);

        if (!$manga) {
            return response()->json(['message' => 'Manga no encontrado'], 404);
        }

        // Eliminar la cover
        if ($manga->cover && Storage::disk('public')->exists($manga->cover)) {
            Storage::disk('public')->delete($manga->cover);
        }

        $manga->delete();

        return to_route('admin.index');
    }

    private function processImage($file)
    {
        // Crear un nombre de archivo único basado en timestamp y un string aleatorio
        $filename = time() . '-' . Str::random(10) . '.webp';
        $path = 'covers/' . $filename;

        //Determinar si es local o producción
        $isLocalEnvironment = app()->environment('local');

        // Definir la ruta base según el entorno
        if ($isLocalEnvironment) {
            $basePath = storage_path('app/public');
        } else {
            $basePath = public_path('storage');
        }
        $directory = $basePath . '/covers';

        // Crear la carpeta si no existe
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Procesar la imagen con Intervention Image
        $image = Image::read($file->getRealPath());

        // Redimensionar a 500px de ancho manteniendo la proporción
        $image = $image->scale(width: 500);

        // Guardar como WebP con calidad 80 (buen equilibrio entre calidad y tamaño)
        $image->toWebp(80)->save($basePath . '/' . $path);

        return $path;
    }

    private function syncRelation($manga, $relation, $items = null, $pivotField = null, $valueField = 'value', $categoryField = 'category'): void
    {
        if ($items === null) {
            $manga->$relation()->detach();
            return;
        }

        if ($pivotField) {
            // Relación con datos en tabla pivot
            $pivotData = [];
            foreach ($items as $item) {
                $pivotData[$item[$valueField]] = [$pivotField => $item[$categoryField]];
            }
            $manga->$relation()->sync($pivotData);
        } else {
            // Relación simple (solo Ids)
            $ids = collect($items)->pluck($valueField)->toArray();
            $manga->$relation()->sync($ids);
        }
    }

    private function syncAlternativeNames($manga, $alternativeNames = null): void
    {
        if ($alternativeNames === null) {
            $manga->names()->delete();
            return;
        }

        $existingNames = $manga->names;
        $keepNameIds = [];

        foreach ($alternativeNames as $item) {
            $existingName = $existingNames->first(function ($name) use ($item) {
                return $name->name === $item['label'] && $name->type === $item['category'];
            });

            if ($existingName) {
                $keepNameIds[] = $existingName->id;
            } else {
                $newName = $manga->names()->create([
                    'name' => $item['label'],
                    'type' => $item['category'],
                ]);
                $keepNameIds[] = $newName->id;
            }
        }

        // Eliminar nombres que ya no existen en la nueva lista
        $manga->names()->whereNotIn('id', $keepNameIds)->delete();
    }
}
