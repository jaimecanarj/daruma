<?php

namespace Database\Factories;

use App\Models\Manga;
use App\Models\Volume;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Volume>
 */
class VolumeFactory extends Factory
{
    protected $model = Volume::class;

    public function definition(): array
    {
        return [
            'manga_id' => Manga::factory(),
            'title' => null,
            'number' => fake()->unique()->numberBetween(1, 500),
            'pages' => fake()->numberBetween(150, 250),
            'owned' => true,
        ];
    }

    public function notOwned(): static
    {
        return $this->state(fn (array $attributes) => [
            'owned' => false,
        ]);
    }
}
