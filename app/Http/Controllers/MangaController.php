<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MangaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Manga::all();
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
            // Guarda el archivo en storage/app/public/covers
            $path = $request->file('cover')->store('covers', 'public');

            // Reemplaza el objeto File con la ruta del archivo
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
    public function show(Manga $manga)
    {
        //
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

            // Guardo el archivo en storage/app/public/covers
            $path = $request->file('cover')->store('covers', 'public');

            // Reemplazo el objeto File con la ruta del archivo
            $validatedData['cover'] = $path;
        } else {
            $validatedData['cover'] = $manga->cover;
        }

        $manga->update([
            'name' => $validatedData['name'],
            'cover' => $validatedData['cover'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'volumes' => $validatedData['volumes'],
            'tankoubon' => $validatedData['tankoubon'],
            'chapters' => $validatedData['chapters'],
            'sinopsis' => $validatedData['sinopsis'],
            'reading_direction' => $validatedData['reading_direction'],
            'language' => $validatedData['language'],
            'finished' => $validatedData['finished'],
            'magazine_id' => $validatedData['magazine_id'],
            'mal' => $validatedData['mal'],
            'listado_manga' => $validatedData['listado_manga'],
        ]);

        //Actualizar relacion names
        if (isset($validatedData['alternative_names'])) {
            // Obtener nombres existentes
            $existingNames = $manga->names;

            // Array para llevar registro de IDs de nombres existentes que se mantienen
            $keepNameIds = [];

            // Verificar nuevos nombres y actualizar o crear según corresponda
            foreach ($validatedData['alternative_names'] as $alternativeName) {
                // Buscar si ya existe un nombre igual
                $existingName = $existingNames->first(function ($name) use ($alternativeName) {
                    return $name->name === $alternativeName['label'] && $name->type === $alternativeName['category'];
                });

                if ($existingName) {
                    // Si existe, lo conservamos (no hacemos nada con él)
                    $keepNameIds[] = $existingName->id;
                } else {
                    // Si no existe, lo creamos
                    $manga->names()->create([
                        'name' => $alternativeName['label'],
                        'type' => $alternativeName['category'],
                    ]);
                }
            }

            // Eliminar solo los nombres que ya no están en la nueva lista
            $manga->names()->whereNotIn('id', $keepNameIds)->delete();
        } else {
            // Si no hay nombres, eliminar todos
            $manga->names()->delete();
        }

        // Actualizar relación people
        if (isset($validatedData['authors'])) {
            $authorPivot = [];
            foreach ($validatedData['authors'] as $author) {
                $authorPivot[$author['value']] = ['job' => $author['category']];
            }
            $manga->people()->sync($authorPivot);
        } else {
            $manga->people()->detach();
        }

        // Actualizar relación tags
        if (isset($validatedData['tags'])) {
            $tagIds = collect($validatedData['tags'])->pluck('value')->toArray();
            $manga->tags()->sync($tagIds);
        } else {
            $manga->tags()->detach();
        }

        // Actualizar relación mangasRelated
        if (isset($validatedData['related_mangas'])) {
            $relatedMangasPivot = [];
            foreach ($validatedData['related_mangas'] as $relatedManga) {
                $relatedMangasPivot[$relatedManga['value']] = ['relation' => $relatedManga['category']];
            }
            $manga->mangasRelated()->sync($relatedMangasPivot);
        } else {
            $manga->mangasRelated()->detach();
        }

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

        $manga->delete();

        return to_route('admin.index');
    }
}
