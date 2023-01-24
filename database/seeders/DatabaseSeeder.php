<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            SuperAdminSeeder::class,
            ServiceUserSeeder::class,
            PracticeSeeder::class,
            ProfileSeeder::class,
            DepartmentSeeder::class,
            JobSpecificationSeeder::class,
            PersonSpecificationSeeder::class,
            AppraisalPolicySeeder::class,
        ]);
    }
}