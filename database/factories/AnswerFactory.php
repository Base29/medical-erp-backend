<?php

namespace Database\Factories;

use App\Models\Answer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Answer::class;

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
            'answer' => $this->faker->paragraph(3, true),
        ];
    }
}