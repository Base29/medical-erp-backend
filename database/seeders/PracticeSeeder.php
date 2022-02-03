<?php

namespace Database\Seeders;

use App\Models\Practice;
use Illuminate\Database\Seeder;

class PracticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Practice::factory()->count(20)->create();
    }
}