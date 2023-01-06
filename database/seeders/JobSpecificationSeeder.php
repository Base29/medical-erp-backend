<?php

namespace Database\Seeders;

use App\Models\JobResponsibility;
use App\Models\JobSpecification;
use Illuminate\Database\Seeder;

class JobSpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Job Specifications
        $jobSpecifications = [
            [
                'title' => 'Job Spec 1',
                'salary_grade' => 'Grade 1',
                'location' => 'Rushden',
                'total_hours' => '40',
                'job_purpose' => 'Jpb specification for spec 1',

            ],
            [
                'title' => 'Job Spec 2',
                'salary_grade' => 'Grade 2',
                'location' => 'Rushden',
                'total_hours' => '40',
                'job_purpose' => 'Jpb specification for spec 2',

            ],
            [
                'title' => 'Job Spec 3',
                'salary_grade' => 'Grade 3',
                'location' => 'Rushden',
                'total_hours' => '40',
                'job_purpose' => 'Jpb specification for spec 3',

            ],
        ];

        // Job Responsibilities
        $responsibilities = [
            'responsibility 1',
            'responsibility 2',
            'responsibility 3',
            'responsibility 4',
            'responsibility 5',
        ];

        foreach ($jobSpecifications as $jobSpecification):
            $createdJobSpecification = JobSpecification::create($jobSpecification);

            foreach ($responsibilities as $responsibility):
                JobResponsibility::create([
                    'job_specification_id' => $createdJobSpecification->id,
                    'responsibility' => $responsibility,
                ]);
            endforeach;
        endforeach;

    }
}