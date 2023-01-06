<?php

namespace Database\Seeders;

use App\Models\PersonSpecification;
use App\Models\PersonSpecificationAttribute;
use Illuminate\Database\Seeder;

class PersonSpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Person specifications
        $personSpecifications = [
            [
                'name' => 'Person Specification 1',
            ],
            [
                'name' => 'Person Specification 2',
            ],
            [
                'name' => 'Person Specification 3',
            ],
            [
                'name' => 'Person Specification 4',
            ],
        ];

        $attributes = [
            [
                'attribute' => 'Education',
                'essential' => 'O Level',
                'desirable' => 'A Level',
            ],
            [
                'attribute' => 'Experience',
                'essential' => '2 Years',
                'desirable' => '4 Years',
            ],
        ];

        foreach ($personSpecifications as $personSpecification):
            $createdPersonSpecification = PersonSpecification::create($personSpecification);

            foreach ($attributes as $attribute):
                PersonSpecificationAttribute::create([
                    'person_specification_id' => $createdPersonSpecification->id,
                    'attribute' => $attribute['attribute'],
                    'essential' => $attribute['essential'],
                    'desirable' => $attribute['desirable'],
                ]);
            endforeach;
        endforeach;
    }
}