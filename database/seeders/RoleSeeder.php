<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Roles
        $roles = [
            'super_admin',
            'admin',
            'headquarter',
            'hq',
            'gp',
            'general_practitioner',
            'nurse',
            'cleaner',
            'global_accountant',
            'local_accountant',
            'supervisor',
            'lead_safeguarding',
            'local_safeguarding',
            'anp',
            'advanced_nurse_practitioner',
            'receptionist',
            'guest',
            'manager',
        ];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
                'guard_name' => 'api',
            ]);
        }
    }
}