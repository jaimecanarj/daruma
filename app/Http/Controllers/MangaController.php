<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MangaController extends Controller
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
        //Validamos los datos recibidos
        $validatedData = $request->validate([
            'cover' => 'required|file|mimes:jpg,jpeg,png,jxl',
            'name' => 'required|string',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
            'volumes' => 'nullable|integer|min:1',
            'tankoubon' => 'nullable|integer|min:1',
            'chapters' => 'nullable|integer|min:1',
            'sinopsis' => 'nullable|string',
            'readingDirection' => ['required', 'string', Rule::in(['ltr', 'rtl'])],
            'language' => ['required', 'string', Rule::in(['es', 'en', 'jp'])],
            'finished' => 'nullable|boolean',
            'magazine' => 'nullable|integer',
            'mal' => 'nullable|integer',
            'listadoManga' => 'nullable|integer',
            'alternativeNames' => 'nullable|array',
            'alternativeNames.*.label' => 'required_with:alternativeNames|string',
            'alternativeNames.*.category' => ['required_with:alternativeNames|string', Rule::in(['japanese', 'spanish', 'other'])],
            'authors' => 'nullable|array',
            'authors.*.value' => 'required_with:authors|integer',
            'authors.*.category' => ['required_with:authors', 'string', Rule::in(['writer', 'illustrator'])],
            'tags' => 'nullable|array',
            'tags.*.value' => 'required_with:tags|integer',
            'relatedMangas' => 'nullable|array',
            'relatedMangas.*.value' => 'required_with:relatedMangas|integer',
            'relatedMangas.*.category' => ['required_with:relatedMangas', 'string', Rule::in(['prequel', 'sequel', 'main story', 'spin-off'])],
        ]);

        if ($request->hasFile('cover')) {
            // Guarda el archivo en storage/app/public/covers
            $path = $request->file('cover')->store('covers', 'public');

            // Reemplaza el objeto File con la ruta del archivo
            $validatedData['cover'] = $path;
        }

        //Almacenamos en la base de datos
        $manga = Manga::create([
            'name' => $validatedData['name'],
            'cover' => $validatedData['cover'],
            'start_date' => $validatedData['startDate'],
            'end_date' => $validatedData['endDate'],
            'volumes' => $validatedData['volumes'],
            'tankoubon' => $validatedData['tankoubon'],
            'chapters' => $validatedData['chapters'],
            'sinopsis' => $validatedData['sinopsis'],
            'reading_direction' => $validatedData['readingDirection'],
            'language' => $validatedData['language'],
            'finished' => $validatedData['finished'],
            'magazine_id' => $validatedData['magazine'],
            'mal' => $validatedData['mal'],
            'listado_manga' => $validatedData['listadoManga'],
        ]);

        if (isset($validatedData['alternativeNames'])) {
            foreach ($validatedData['alternativeNames'] as $alternativeName) {
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
            $tags = [];
            foreach ($validatedData['tags'] as $tag) {
                $tags[] = $tag['value'];
            }
            $manga->tags()->attach($tags);
        }

        if (isset($validatedData['relatedMangas'])) {
            $mangas = [];
            foreach ($validatedData['relatedMangas'] as $relatedManga) {
                $mangas[$relatedManga['value']] = ['relation' => $relatedManga['category']];
            }
            $manga->mangasRelated()->attach($mangas);
        }

        return to_route('admin.create');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manga $manga)
    {
        //
    }
}
