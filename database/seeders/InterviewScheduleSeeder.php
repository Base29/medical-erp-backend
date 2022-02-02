<?php

namespace Database\Seeders;

use App\Models\InterviewSchedule;
use Illuminate\Database\Seeder;

class InterviewScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InterviewSchedule::factory()->count(40)->create();
    }
}