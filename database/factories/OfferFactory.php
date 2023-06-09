<?php

namespace Database\Factories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Offer::class;

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
            'user_id' => $this->faker->biasedNumberBetween(6, 26),
            'work_pattern_id' => 1,
            'amount' => '3000',
        ];
    }
}