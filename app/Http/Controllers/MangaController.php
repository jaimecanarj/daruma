<?php

namespace App\Http\Controllers;

use App\Models\Magazine;
use App\Models\Manga;
use App\Models\Person;
use App\Models\Tag;
use App\Models\Volume;
use App\Traits\ImageProcessorTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MangaController extends Controller
{
    use ImageProcessorTrait;

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

            $relations = ['tags', 'people', 'magazine'];
            if (auth()->check()) {
                $relations[] = 'currentUserData';
            }
            $query = Manga::with($relations);

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

            //Filtro de etiquetas
            if (!empty($request->tags)) {
                // Si hay etiquetas para incluir
                if (!empty($request->tags['include'])) {
                    $includeTags = $request->tags['include'];
                    $query->whereHas(
                        'tags',
                        function ($q) use ($includeTags) {
                            $q->whereIn('id', $includeTags);
                        },
                        '=',
                        count($includeTags)
                    );
                }

                // Si hay etiquetas para excluir
                if (!empty($request->tags['exclude'])) {
                    $excludeTags = $request->tags['exclude'];
                    $query->whereDoesntHave('tags', function ($q) use ($excludeTags) {
                        $q->whereIn('id', $excludeTags);
                    });
                }
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

            //Listado completo
            $mangasIds = [];
            if ($page == 1) {
                $mangasIds = (clone $query)->pluck('id')->toArray();
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

            $paginatedResults = $query->paginate(28, page: $page)->toArray();

            return [...$paginatedResults, 'mangas_ids' => $mangasIds];
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

    public function store(Request $request)
    {
        //Validamos los datos recibidos
        $validatedData = $request->validate([
            'cover' => 'required|file|mimes:jpg,jpeg,png,jxl,webp|max:5120',
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
            'volumes_data' => 'nullable|array',
            'volumes_data.*.name' => 'required_with:volumes_data|string',
            'volumes_data.*.cover' => 'required|file|mimes:jpg,jpeg,png,jxl,webp',
            'volumes_data.*.order' => 'required_with:volumes_data|integer|min:1',
            'volumes_data.*.date' => 'nullable|date',
            'volumes_data.*.pages' => 'required_with:volumes_data|integer|min:1',
            'volumes_data.*.chapters' => 'nullable|array',
            'volumes_data.*.chapters.*.name' => 'required_with:volumesData.*.chapters|string',
            'volumes_data.*.chapters.*.order' => 'required_with:volumesData.*.chapters|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($validatedData, $request) {
                if ($request->hasFile('cover')) {
                    // Procesar y optimizar la imagen
                    $path = $this->processImage($request->file('cover'), 'covers');

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

                if (isset($validatedData['volumes_data'])) {
                    $volumeController = app(VolumeController::class);

                    foreach ($validatedData['volumes_data'] as $index => $volumeData) {
                        $volumeData['manga_id'] = $manga->id;

                        // Crear una nueva request con los datos
                        $volumeRequest = new Request($volumeData);

                        // Adjuntar la portada a la nueva request
                        if ($request->hasFile("volumesData.{$index}.cover")) {
                            $file = $request->file("volumesData.{$index}.cover");
                            $volumeRequest->files->set('cover', $file);
                        }

                        $volumeController->store($volumeRequest);
                    }
                }
            });

            return to_route('admin.create', ['tab' => 'manga']);
        } catch (QueryException $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al guardar el manga. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }

    public function show($id)
    {
        return Inertia::render('manga/Show', [
            'manga' => Inertia::defer(
                fn() => Manga::find($id)->load('tags', 'people', 'magazine', 'names', 'mangasRelated', 'volumesData', 'chaptersData')
            ),
        ]);
    }

    public function update(Request $request, Manga $manga)
    {
        $validatedData = $request->validate([
            'cover' => 'nullable|file|mimes:jpg,jpeg,png,jxl,webp|max:5120',
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
            'volumes_data' => 'nullable|array',
            'volumes_data.*.name' => 'required_with:volumes_data|string',
            'volumes_data.*.cover' => 'nullable|file|mimes:jpg,jpeg,png,jxl,webp',
            'volumes_data.*.cover_url' => 'nullable|string',
            'volumes_data.*.order' => 'required_with:volumes_data|integer|min:1',
            'volumes_data.*.date' => 'nullable|date',
            'volumes_data.*.pages' => 'required_with:volumes_data|integer|min:1',
            'volumes_data.*.chapters' => 'nullable|array',
            'volumes_data.*.chapters.*.name' => 'required_with:volumes_data.*.chapters|string',
            'volumes_data.*.chapters.*.order' => 'required_with:volumesData.*.chapters|integer|min:1',
        ]);

        try {
            //Compruebo que se ha pasado una nueva cover
            if ($request->hasFile('cover') && $request->file('cover')->getSize() > 0) {
                //Borro el cover actual
                if (Storage::disk('public')->exists($manga->cover)) {
                    Storage::disk('public')->delete($manga->cover);
                }

                // Procesar y optimizar la imagen
                $path = $this->processImage($request->file('cover'), 'covers');

                // Reemplazar con la ruta del archivo optimizado
                $validatedData['cover'] = $path;
            } else {
                $validatedData['cover'] = $manga->cover;
            }

            $manga->update($validatedData);

            // Forzar actualización del timestamp
            $manga->touch();

            // Actualizar relaciones
            $this->syncAlternativeNames($manga, $validatedData['alternative_names'] ?? null);
            $this->syncRelation($manga, 'people', $validatedData['authors'] ?? null, 'job');
            $this->syncRelation($manga, 'tags', $validatedData['tags'] ?? null);
            $this->syncRelation($manga, 'mangasRelated', $validatedData['related_mangas'] ?? null, 'relation');

            if (isset($validatedData['volumes_data'])) {
                $volumeController = app(VolumeController::class);

                // Hacer todas las llamadas en una sola transacción
                DB::transaction(function () use ($volumeController, $validatedData, $manga, $request) {
                    // Eliminar todos los volúmenes existentes
                    $manga->volumesData()->delete();
                    //TODO: No borrar los tomos que ya tienen un volumen asociado porque destroza los timestamps

                    foreach ($validatedData['volumes_data'] as $index => $volumeData) {
                        $volumeData['manga_id'] = $manga->id;

                        // Crear una nueva request con los datos
                        $volumeRequest = new Request($volumeData);

                        // Adjuntar la portada a la nueva request
                        if ($request->hasFile("volumesData.{$index}.cover")) {
                            $file = $request->file("volumesData.{$index}.cover");
                            $volumeRequest->files->set('cover', $file);
                        }

                        $volumeController->update($volumeRequest);
                    }
                });
            }

            return to_route('admin.index', ['tab' => 'manga']);
        } catch (QueryException $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al actualizar el manga. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer',
        ]);

        $manga = Manga::find($validatedData['id']);

        if (!$manga) {
            throw ValidationException::withMessages([
                'general' => ['Manga no encontrado.'],
            ]);
        }
        try {
            // Eliminar la cover
            if ($manga->cover && Storage::disk('public')->exists($manga->cover)) {
                Storage::disk('public')->delete($manga->cover);
            }

            $manga->delete();

            return to_route('admin.index');
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al eliminar el manga. Por favor, inténtalo de nuevo.'],
            ]);
        }
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
