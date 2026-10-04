<?php

namespace Database\Factories;

use App\Models\Bookmark;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bookmark>
 */
class BookmarkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'collection_id' => fn (array $attributes) => Collection::factory()->create(['user_id' => $attributes['user_id']])->id,
            'title' => fake()->sentence(4),
            'note' => fake()->optional()->paragraph(),
            'url' => fake()->url(),
            'is_favorite' => fake()->boolean(20),
        ];
    }
}
