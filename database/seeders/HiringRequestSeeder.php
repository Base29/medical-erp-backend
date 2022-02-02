<?php

namespace Database\Seeders;

use App\Models\HiringRequest;
use Illuminate\Database\Seeder;

class HiringRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        HiringRequest::factory()->count(20)->create();
    }
}