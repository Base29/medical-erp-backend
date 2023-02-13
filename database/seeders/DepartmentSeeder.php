<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Departments
        $departments = [
            'Department 1',
            'Department 2',
        ];

        // Department head
        $departmentHead = User::where('email', 'manager@eharleystreetadmin.com')->firstOrFail();

        // Fetch practices
        $practices = Practice::get();

        foreach ($practices as $practice):
            foreach ($departments as $department):
                Department::create([
                    'user_id' => $departmentHead->id,
                    'practice_id' => $practice->id,
                    'name' => $department,
                ]);
            endforeach;
        endforeach;
    }
}