<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperadminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Super Admin
        $superAdmin = User::create([
            'email' => 'faisal.hussain@itroadway.com',
            'email_verified_at' => now(),
            'is_active' => 1,
            'is_candidate' => 0,
            'is_hired' => 0,
            'password' => Hash::make('testpass'), // password
            'remember_token' => Str::random(10),
        ]);

        $superAdmin->assignRole('super_admin');
    }
}