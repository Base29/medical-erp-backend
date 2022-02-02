<?php

namespace Database\Factories;

use App\Models\InterviewSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class InterviewScheduleFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InterviewSchedule::class;

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
            'interview_id' => 1,
            'user_id' => 6,
            'date' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            'time' => $this->faker->time($format = 'H:i:s', $max = 'now'),
            'location' => $this->faker->city(),
            'interview_type' => 'digital-interview',
            'application_status' => 'second-interview',
        ];
    }
}