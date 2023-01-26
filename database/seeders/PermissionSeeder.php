<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Permissions
        $permissions = [
            'can_update_task',
            'can_update_room',
            'can_view_own_posts',
            'can_create_post',
            'can_update_own_post',
            'can_delete_own_post',
            'can_fetch_communication_book_posts',
            'can_fetch_posts',
            'can_fetch_own_posts',
            'can_view_post',
            'can_update_post',
            'can_create_comment',
            'can_create_answer',
            'can_manage_role',
            'can_manage_permission',
            'can_manage_user',
            'can_view_practices',
            'can_create_practice',
            'can_delete_practice',
            'can_assign_practice',
            'can_revoke_practice',
            'can_create_policy',
            'can_delete_policy',
            'can_view_policies',
            'can_sign_policy',
            'can_view_rooms',
            'can_create_room',
            'can_delete_room',
            'can_view_reasons',
            'can_create_reason',
            'can_delete_reason',
            'can_create_task',
            'can_delete_task',
            'can_view_checklists',
            'can_fetch_signatures',
            'can_create_contract_summary',
            'can_update_contract_summary',
            'can_fetch_single_contract_summary',
            'can_delete_contract_summary',
            'can_create_position_summary',
            'can_update_position_summary',
            'can_fetch_single_position_summary',
            'can_delete_position_summary',
            'can_view_own_profile',
            'can_fetch_work_patterns',
            'can_update_profile',
            'can_create_misc_info',
            'can_fetch_misc_info',
            'can_delete_misc_info',
            'can_create_employment_check',
            'can_update_employment_check',
            'can_delete_employment_check',
            'can_fetch_single_employment_check',
            'can_create_employment_policy',
            'can_update_employment_policy',
            'can_delete_employment_policy',
            'can_fetch_employment_policies',
            'can_create_employment_history',
            'can_update_employment_history',
            'can_delete_employment_history',
            'can_fetch_employment_history',
            'can_fetch_single_employment_history',
            'can_update_misc_info',
            'can_create_reference',
            'can_update_reference',
            'can_delete_reference',
            'can_create_education',
            'can_update_education',
            'can_fetch_education',
            'can_delete_education',
            'can_fetch_user_references',
            'can_create_emergency_contact',
            'can_update_emergency_contact',
            'can_delete_emergency_contact',
            'can_create_termination',
            'can_update_termination',
            'can_delete_termination',
            'can_create_hiring_request',
            'can_fetch_hiring_request',
            'can_update_hiring_request',
            'can_delete_hiring_request',
            'can_fetch_emergency_contact',
            'can_fetch_termination',
            'can_fetch_user_legal',
            'can_create_legal',
            'can_delete_legal',
            'can_update_legal',
            'can_create_department',
            'can_fetch_department',
            'can_delete_department',
            'can_create_job_specification',
            'can_fetch_job_specification',
            'can_delete_job_specification',
            'can_create_person_specification',
            'can_fetch_person_specification',
            'can_delete_person_specification',
            'can_process_hiring_request',
            'can_create_induction_checklist',
            'can_fetch_induction_checklists',
            'can_fetch_single_induction_checklist',
            'can_delete_induction_checklist',
            'can_update_induction_checklist',
            'can_create_induction_schedule',
            'can_fetch_practice_induction_schedules',
            'can_create_induction_result',
            'can_fetch_single_hiring_request',
            'can_fetch_interviews',
            'can_fetch_interview_schedules',
            'can_fetch_offers',
            'can_fetch_single_department',
            'can_fetch_single_job_specification',
            'can_fetch_single_person_specification',
            'can_update_interview',
            'can_delete_interview',
            'can_create_interview',
            'can_create_interview_policy',
            'can_fetch_interview_policies',
            'can_fetch_single_interview_policy',
            'can_fetch_upcoming_interviews',
            'can_fetch_all_interviews',
            'can_fetch_postings',
            'can_create_posting',
            'can_fetch_applicants',
            'can_create_offer',
            'can_update_offer',
            'can_delete_offer',
            'can_fetch_single_offer',
            'can_update_interview_policy_question',
            'can_update_interview_policy',
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
            'can_manage_job_specifications',
            'can_manage_person_specifications',
            'can_manage_offers',
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
            'can_manage_inductions',
            'can_manage_departments',
            'can_manage_user_objective',
            'can_manage_postings',
            'can_manage_own_profile',
            'can_manage_own_trainings',
            'can_manage_own_locum_sessions',
            'can_manage_own_policies',
            'can_manage_own_notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'api',
            ]);
        }
    }
}