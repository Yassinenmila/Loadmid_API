<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'titre' => $this->faker->sentence(8),
            'contenu' => implode('\n\n',$this->faker->paragraphs(3)),
            'location'=>$this->faker->city(),
            'user_id' => User::inRandomOrder()->value('id'),
        ];
    }
}
