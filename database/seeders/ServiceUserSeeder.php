<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ServiceUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Manager
        $manager = User::create([
            'email' => 'manager@eharleystreetadmin.com',
            'email_verified_at' => now(),
            'is_active' => 1,
            'is_candidate' => 0,
            'is_hired' => 0,
            'password' => Hash::make('testpass'), // password
            'remember_token' => Str::random(10),
        ]);

        $manager->assignRole(['manager']);

        // Create Recruiter
        $recruiter = User::create([
            'email' => 'recruiter@eharleystreetadmin.com',
            'email_verified_at' => now(),
            'is_active' => 1,
            'is_candidate' => 0,
            'is_hired' => 0,
            'password' => Hash::make('testpass'), // password
            'remember_token' => Str::random(10),
        ]);

        $recruiter->assignRole(['recruiter']);

        // Create Manager
        $hq = User::create([
            'email' => 'hq@eharleystreetadmin.com',
            'email_verified_at' => now(),
            'is_active' => 1,
            'is_candidate' => 0,
            'is_hired' => 0,
            'password' => Hash::make('testpass'), // password
            'remember_token' => Str::random(10),
        ]);

        $hq->assignRole(['hq']);
    }
}