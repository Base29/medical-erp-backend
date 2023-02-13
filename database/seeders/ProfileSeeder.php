<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Profile::create([
            'user_id' => 2,
            'first_name' => 'User',
            'last_name' => 'Manager',
            'primary_role' => 'manager',
            'profile_image' => 'https://i.imgur.com/sj6jKyB.png',
        ]);

        Profile::create([
            'user_id' => 3,
            'first_name' => 'User',
            'last_name' => 'Recruiter',
            'primary_role' => 'recruiter',
            'profile_image' => 'https://i.imgur.com/sj6jKyB.png',
        ]);

        Profile::create([
            'user_id' => 4,
            'first_name' => 'User',
            'last_name' => 'HQ',
            'primary_role' => 'hq',
            'profile_image' => 'https://i.imgur.com/9os1M6v.jpeg',
        ]);
    }
}