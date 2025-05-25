<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Géneros
        $genres = [
            ['name' => 'Acción', 'type' => 'genre'],
            ['name' => 'Aventura', 'type' => 'genre'],
            ['name' => 'Comedia', 'type' => 'genre'],
            ['name' => 'Deportes', 'type' => 'genre'],
            ['name' => 'Drama', 'type' => 'genre'],
            ['name' => 'Ecchi', 'type' => 'genre'],
            ['name' => 'Fantasía', 'type' => 'genre'],
            ['name' => 'Filosófico', 'type' => 'genre'],
            ['name' => 'Harem', 'type' => 'genre'],
            ['name' => 'Misterio', 'type' => 'genre'],
            ['name' => 'Psicológico', 'type' => 'genre'],
            ['name' => 'Recuentos de la vida', 'type' => 'genre'],
            ['name' => 'Romance', 'type' => 'genre'],
            ['name' => 'Sci-Fi', 'type' => 'genre'],
            ['name' => 'Sobrenatural', 'type' => 'genre'],
            ['name' => 'Suspense', 'type' => 'genre'],
            ['name' => 'Terror', 'type' => 'genre'],
            ['name' => 'Tragedia', 'type' => 'genre'],
            ['name' => 'Yaoi', 'type' => 'genre'],
            ['name' => 'Yuri', 'type' => 'genre'],
        ];

        Tag::insert($genres);

        // Temas
        $themes = [
            ['name' => 'Aliens', 'type' => 'theme'],
            ['name' => 'Animales', 'type' => 'theme'],
            ['name' => 'Artes marciales', 'type' => 'theme'],
            ['name' => 'Cocina', 'type' => 'theme'],
            ['name' => 'Chicas mágicas', 'type' => 'theme'],
            ['name' => 'Delincuentes', 'type' => 'theme'],
            ['name' => 'Demonios', 'type' => 'theme'],
            ['name' => 'Detectives', 'type' => 'theme'],
            ['name' => 'Fantasmas', 'type' => 'theme'],
            ['name' => 'Gore', 'type' => 'theme'],
            ['name' => 'Histórico', 'type' => 'theme'],
            ['name' => 'Isekai', 'type' => 'theme'],
            ['name' => 'Mafia', 'type' => 'theme'],
            ['name' => 'Magia', 'type' => 'theme'],
            ['name' => 'Mecha', 'type' => 'theme'],
            ['name' => 'Medicina', 'type' => 'theme'],
            ['name' => 'Militar', 'type' => 'theme'],
            ['name' => 'Mitológico', 'type' => 'theme'],
            ['name' => 'Monstruos', 'type' => 'theme'],
            ['name' => 'Música', 'type' => 'theme'],
            ['name' => 'Ninja', 'type' => 'theme'],
            ['name' => 'Oficina', 'type' => 'theme'],
            ['name' => 'Policíaco', 'type' => 'theme'],
            ['name' => 'Post-apocalíptico', 'type' => 'theme'],
            ['name' => 'Reencarnación', 'type' => 'theme'],
            ['name' => 'Samurai', 'type' => 'theme'],
            ['name' => 'Superhéroes', 'type' => 'theme'],
            ['name' => 'Supervivencia', 'type' => 'theme'],
            ['name' => 'Vampiros', 'type' => 'theme'],
            ['name' => 'Viajes en el tiempo', 'type' => 'theme'],
            ['name' => 'Vida escolar', 'type' => 'theme'],
            ['name' => 'Zombies', 'type' => 'theme'],
        ];

        Tag::insert($themes);
    }
}
