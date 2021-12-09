<?php

namespace App\Http\Controllers\EmploymentHistory;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmploymentHistory\CreateEmploymentHistoryRequest;
use App\Http\Requests\EmploymentHistory\UpdateEmploymentHistoryRequest;
use App\Models\EmploymentHistory;
use App\Models\User;

class EmploymentHistoryController extends Controller
{
    // Create employment history
    public function create(CreateEmploymentHistoryRequest $request)
    {
        try {

            // Get user
            $user = User::findOrFail($request->user);

            // Create instance of Employment History
            $employmentHistory = new EmploymentHistory();
            $employmentHistory->employer_name = $request->employer_name;
            $employmentHistory->address = $request->address;
            $employmentHistory->phone_number = $request->phone_number;
            $employmentHistory->type_of_business = $request->type_of_business;
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

            // Return success response
            return Response::success([
                'employment-history' => $employmentHistory,
            ]);

        } catch (\Exception$e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update employment history
    public function update(UpdateEmploymentHistoryRequest $request)
    {
        try {

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
                return Response::fail([
                    'message' => ResponseMessage::allowedFields($allowedFields),
                    'code' => 400,
                ]);
            }

            // Get employment history by Id ($request->employment_history)
            $employmentHistory = EmploymentHistory::findOrFail($request->employment_history);

            // Update $employmentHistory
            $employmentHistoryUpdated = UpdateService::updateModel($employmentHistory, $request->all(), 'employment_history');

            if (!$employmentHistoryUpdated) {
                return Response::fail([
                    'code' => 400,
                    'message' => ResponseMessage::customMessage('Something went wrong. Cannot update Employment History.'),
                ]);
            }

            // Return success response
            return Response::success([
                'employment-history' => $employmentHistory->latest('updated_at')->first(),
            ]);

        } catch (\Exception$e) {
            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}