<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

trait ImageProcessorTrait
{
    protected function processImage($file, $folder)
    {
        // Crear un nombre de archivo único basado en timestamp y un string aleatorio
        $filename = time() . '-' . Str::random(10) . '.webp';
        $path = $folder . '/' . $filename;

        //Determinar si es local o producción
        $isLocalEnvironment = app()->environment('local');

        // Definir la ruta base según el entorno
        if ($isLocalEnvironment) {
            $basePath = storage_path('app/public');
        } else {
            $basePath = public_path('storage');
        }
        $directory = $basePath . '/' . $folder;

        // Crear la carpeta si no existe
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Procesar la imagen con Intervention Image
        $image = Image::read($file->getRealPath());

        // Redimensionar a 500px de ancho manteniendo la proporción
        $image = $image->scale(width: 500);

        // Guardar como WebP
        $image->toWebp()->save($basePath . '/' . $path);

        return $path;
    }
}
