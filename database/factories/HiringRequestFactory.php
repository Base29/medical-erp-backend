<?php

namespace Database\Factories;

use App\Models\HiringRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class HiringRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HiringRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'practice_id' => 1,
            'department_id' => 2,
            'job_title' => $this->faker->jobTitle(),
            'contract_type' => "permanent",
            'reporting_to' => 1,
            'start_date' => $this->faker->date('Y-m-d', now()),
            'starting_salary' => '1000-2000',
            'reason_for_recruitment' => $this->faker->realText(200, 2),
            'comment' => $this->faker->realText(200, 2),
            'job_specification_id' => 1,
            'person_specification_id' => 2,
            'rota_information' => 1,
        ];
    }
}