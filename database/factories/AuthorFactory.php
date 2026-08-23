<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Author>
 */
class AuthorFactory extends Factory
{
    protected $model = Author::class;

    public function definition(): array
    {
        $name = fake()->unique()->name();

        return [
            'slug' => Str::slug($name),
            'name' => $name,
            'name_japanese' => null,
        ];
    }
}
