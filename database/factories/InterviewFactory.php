<?php

namespace Database\Factories;

use App\Models\Interview;
use Illuminate\Database\Eloquent\Factories\Factory;

class InterviewFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Interview::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'practice_id' => 1,
            'hiring_request_id' => 4,
            'name' => $this->faker->name(),
        ];
    }
}