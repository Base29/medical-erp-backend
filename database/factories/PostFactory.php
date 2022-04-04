<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->realText(20, 2),
            'subject' => $this->faker->realText(50, 2),
            'message' => $this->faker->realText(200, 2),
            'category' => 'support',
            'type' => 'communication_book',
            'user_id' => $this->faker->biasedNumberBetween(6, 26),
            'practice_id' => $this->faker->biasedNumberBetween(1, 21),
        ];
    }
}