<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'post_id' => $this->faker->biasedNumberBetween(1, 51),
            'user_id' => $this->faker->biasedNumberBetween(6, 26),
            'comment' => $this->faker->realText(200, 2),
        ];
    }
}