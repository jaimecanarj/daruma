<?php

namespace Database\Factories;

use App\Enums\MangaLanguage;
use App\Enums\ReadingDirection;
use App\Models\Manga;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Manga>
 */
class MangaFactory extends Factory
{
    protected $model = Manga::class;

    public function definition(): array
    {
        /** @var string $title */
        $title = fake()->unique()->words(3, true);
        $title = ucwords($title);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'nas_folder_name' => $title,
            'synopsis' => fake()->paragraphs(2, true),
            'start_date' => fake()->dateTimeBetween('-30 years', '-1 years'),
            'end_date' => null,
            'reading_direction' => ReadingDirection::Rtl->value,
            'volumes_edition' => fake()->numberBetween(1, 50),
            'volumes_tankoubon' => fake()->numberBetween(1, 50),
            'chapters' => fake()->numberBetween(10, 500),
            'finished' => fake()->boolean(30),
            'language' => fake()->randomElement(MangaLanguage::cases())->value,
            'magazine' => fake()->company(),
        ];
    }

    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'finished' => true,
            'end_date' => fake()->dateTimeBetween($attributes['start_date'], 'now'),
        ]);
    }
}
