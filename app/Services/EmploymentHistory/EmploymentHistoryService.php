<?php
namespace App\Services\EmploymentHistory;

use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\EmploymentHistory;
use App\Models\User;

class EmploymentHistoryService
{
    // Create employment history
    public function createEmploymentHistory($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Create instance of Employment History
        $employmentHistory = new EmploymentHistory();
        $employmentHistory->employer_name = $request->employer_name;
        $employmentHistory->address = $request->address;
        $employmentHistory->phone_number = $request->phone_number;
        $employmentHistory->type_of_business = $request->type_of_business;
        $employmentHistory->job_title = $request->job_title;
        $employmentHistory->job_start_date = $request->job_start_date;
        $employmentHistory->job_end_date = $request->job_end_date;
        $employmentHistory->salary = $request->salary;
        $employmentHistory->reporting_to = $request->reporting_to;
        $employmentHistory->period_of_notice = $request->period_of_notice;
        $employmentHistory->can_contact_referee = $request->can_contact_referee;
        $employmentHistory->reason_for_leaving = $request->reason_for_leaving;
        $employmentHistory->responsibilities_duties_desc = $request->responsibilities_duties_desc;
        $employmentHistory->is_current = $request->is_current;

        // Save user employment history
        $user->employmentHistories()->save($employmentHistory);

        // Return employment history
        return $employmentHistory;
    }

    // Update employment history
    public function updateEmploymentHistory($request)
    {
        // Allowed fields
        $allowedFields = [
            'employer_name',
            'address',
            'phone_number',
            'type_of_business',
            'job_title',
            'job_start_date',
            'job_end_date',
            'salary',
            'reporting_to',
            'period_of_notice',
            'can_contact_referee',
            'reason_for_leaving',
            'responsibilities_duties_desc',
            'is_current',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get employment history by Id ($request->employment_history)
        $employmentHistory = EmploymentHistory::findOrFail($request->employment_history);

        // Update $employmentHistory
        $employmentHistoryUpdated = UpdateService::updateModel($employmentHistory, $request->validated(), 'employment_history');

        if (!$employmentHistoryUpdated) {
            throw new \Exception(ResponseMessage::customMessage('Something went wrong. Cannot update Employment History.'));
        }

        // Return success response
        return $employmentHistory->latest('updated_at')->first();

    }

    // Delete employment history
    public function deleteEmploymentHistory($request)
    {
        // Get employment history by ID ($request->employment_history)
        $employmentHistory = EmploymentHistory::findOrFail($request->employment_history);

        // Delete employment history
        $employmentHistory->delete();
    }

    // Fetch employment history
    public function fetchEmploymentHistory($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Get user's employment histories
        return EmploymentHistory::where('user_id', $user->id)->latest()->get();
    }

    // Fetch single employment history
    public function fetchSingleEmploymentHistory($request)
    {
        // Get employment history
        return EmploymentHistory::where('id', $request->employment_history)->get();
    }
}
