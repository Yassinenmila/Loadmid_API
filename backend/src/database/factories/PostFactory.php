<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'titre' => fake()->sentence(8),
            'contenu' => implode("\n\n", fake()->paragraphs(3)),
            'location' => fake()->city(),
            'user_id' => User::factory(),
        ];
    }
}
