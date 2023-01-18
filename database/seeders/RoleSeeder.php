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

        // Permissions for HQ
        $permissionsForHq = [
            'can_manage_hiring_requests',
            'can_manage_interview',
            'can_manage_offers',
            'can_fetch_department',
        ];

        // Permissions for recruiter
        $permissionsForRecruiter = [
            'can_manage_interview',
            'can_manage_interview_policy',
            'can_manage_locums',
            'can_manage_employee_handbook',
            'can_manage_it_policy',
            'can_manage_candidate',
            'can_manage_hiring_requests',
            'can_manage_contract_summaries',
            'can_manage_position_summaries',
            'can_manage_work_patterns',
            'can_manage_profiles',
            'can_manage_misc_info',
            'can_manage_employment_checks',
            'can_manage_employment_policies',
            'can_manage_employment_histories',
            'can_manage_references',
            'can_manage_education',
            'can_manage_legal',
            'can_manage_emergency_contacts',
            'can_manage_terminations',
            'can_manage_hiring_requests',
            'can_manage_job_specifications',
            'can_manage_person_specifications',
            'can_manage_offers',
            'can_manage_role',
            'can_fetch_department',
            'can_view_practices',
            'can_manage_user',
            'can_fetch_single_job_specification',
            'can_fetch_single_person_specification',
            'can_manage_postings',
        ];

        // Permissions for manager
        $permissionsForManager = [
            'can_manage_appraisal',
            'can_manage_appraisal_policy',
            'can_manage_training_course',
            'can_manage_employees',
            'can_manage_practices',
            'can_manage_policies',
            'can_manage_rooms',
            'can_manage_reasons',
            'can_manage_checklists',
            'can_manage_tasks',
            'can_manage_posts',
            'can_manage_answers',
            'can_manage_comments',
            'can_manage_signatures',
            'can_manage_work_patterns',
            'can_manage_hiring_requests',
            'can_manage_inductions',
            'can_manage_departments',
            'can_manage_user_objective',
            'can_fetch_interviews',
            'can_manage_role',
            'can_fetch_job_specification',
            'can_fetch_person_specification',
            'can_fetch_single_job_specification',
            'can_fetch_single_person_specification',
            'can_manage_user',
        ];

        // Roles
        $roles = [
            'super_admin',
            'admin',
            'hq',
            'general_practitioner',
            'nurse',
            'cleaner',
            'global_accountant',
            'local_accountant',
            'supervisor',
            'lead_safeguarding',
            'local_safeguarding',
            'advanced_nurse_practitioner',
            'receptionist',
            'guest',
            'manager',
            'recruiter',
        ];

        foreach ($roles as $role) {
            $createdRole = Role::create([
                'name' => $role,
                'guard_name' => 'api',
            ]);

            /**
             * Assign permissions to roles
             */

            // Assign permissions to hq, headquarter role
            if ($createdRole->name === 'hq') {
                $createdRole->syncPermissions($permissionsForHq);
            }

            // Assign permissions to re, recruiter role
            if ($createdRole->name === 'recruiter') {
                $createdRole->syncPermissions($permissionsForRecruiter);
            }

            // Assign permission to ma, manager role
            if ($createdRole->name === 'manager') {
                $createdRole->syncPermissions($permissionsForManager);
            }
        }
    }
}