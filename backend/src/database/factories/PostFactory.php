<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
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
