<?php

namespace App\Http\Controllers\EmploymentHistory;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmploymentHistory\CreateEmploymentHistoryRequest;
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
}