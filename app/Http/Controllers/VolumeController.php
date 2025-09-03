<?php

namespace App\Http\Controllers;

use App\Models\Volume;
use App\Traits\ImageProcessorTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class VolumeController extends Controller
{
    use ImageProcessorTrait;

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //Validamos los datos recibidos
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'cover' => 'required|image|mimes:jpeg,png,jpg,jxl,webp|max:2048',
            'order' => 'required|integer|min:1',
            'date' => 'nullable|date',
            'pages' => 'required|integer|min:1',
            'chapters' => 'nullable|array',
            'chapters.*.name' => 'required_with:chapters|string|max:255',
            'chapters.*.order' => 'required_with:chapters|integer|min:1',
            'manga_id' => 'required|integer|exists:mangas,id',
        ]);

        try {
            //Guardar portada
            if ($request->hasFile('cover')) {
                // Procesar y optimizar la imagen
                $path = $this->processImage($request->file('cover'), 'covers/volumes');

                // Reemplazar con la ruta del archivo optimizado
                $validatedData['cover'] = $path;
            }

            //Almacenamos en la base de datos
            $volume = Volume::create($validatedData);

            if (isset($validatedData['chapters'])) {
                $chapterController = app(ChapterController::class);

                // Hacer todas las llamadas en una sola transacción
                DB::transaction(function () use ($chapterController, $validatedData, $volume) {
                    foreach ($validatedData['chapters'] as $chapter) {
                        $chapter['manga_id'] = $volume->manga_id;
                        $chapter['volume_order'] = $volume->order;

                        $chapterController->store(new Request($chapter));
                    }
                });
            }
        } catch (QueryException $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al guardar el tomo. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }

    public function show(Volume $volume)
    {
        //
    }

    public function update(Request $request)
    {
        //Validamos los datos recibidos
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,jxl,webp|max:2048',
            'cover_url' => 'nullable|string',
            'order' => 'required|integer|min:1',
            'date' => 'nullable|date',
            'pages' => 'required|integer|min:1',
            'chapters' => 'nullable|array',
            'chapters.*.name' => 'required_with:chapters|string|max:255',
            'chapters.*.order' => 'required_with:chapters|integer|min:1',
            'manga_id' => 'required|integer|exists:mangas,id',
        ]);

        try {
            //Guardar portada
            if ($request->hasFile('cover') && $request->file('cover')->getSize() > 0) {
                if (isset($validatedData['cover_url'])) {
                    //Borro el cover actual
                    if (Storage::disk('public')->exists($validatedData['cover_url'])) {
                        Storage::disk('public')->delete($validatedData['cover_url']);
                    }
                }

                // Procesar y optimizar la imagen
                $path = $this->processImage($request->file('cover'), 'covers/volumes');

                // Reemplazar con la ruta del archivo optimizado
                $validatedData['cover'] = $path;
            } else {
                $validatedData['cover'] = $validatedData['cover_url'];
            }

            //Almacenamos en la base de datos
            $volume = Volume::create($validatedData);

            if (isset($validatedData['chapters'])) {
                $chapterController = app(ChapterController::class);

                // Hacer todas las llamadas en una sola transacción
                DB::transaction(function () use ($chapterController, $validatedData, $volume) {
                    // Eliminar todos los capítulos existentes
                    $volume->chapters()->delete();

                    foreach ($validatedData['chapters'] as $chapter) {
                        $chapter['manga_id'] = $volume->manga_id;
                        $chapter['volume_order'] = $volume->order;

                        $chapterController->update(new Request($chapter));
                    }
                });
            }
        } catch (QueryException $e) {
            throw ValidationException::withMessages([
                'general' => ['Error al actualizar un tomo. Por favor, inténtalo de nuevo.'],
            ]);
        }
    }

    public function destroy(Volume $volume)
    {
        //
    }
}
