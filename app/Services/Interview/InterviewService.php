<?php
namespace App\Services\Interview;

use App\Helpers\Response;
use App\Models\Department;
use App\Models\HiringRequest;
use App\Models\Interview;
use App\Models\InterviewPolicy;
use App\Models\InterviewSchedule;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Support\Carbon;

class InterviewService
{
    // Fetch all of practice's interviews
    public function fetchPracticeInterviews($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get $practice interviews
        $interviews = Interview::where('practice_id', $practice->id)
            ->with('practice')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'interviews' => $interviews,
        ]);
    }

    // Fetch interview schedules for a practice
    public function fetchUpcomingInterviewSchedules($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get $practice interview schedules
        $interviewSchedules = InterviewSchedule::where('practice_id', $practice->id)
            ->where('date', '>', Carbon::now())
            ->with('practice', 'interviewPolicy', 'user', 'hiringRequest')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'interview-schedules' => $interviewSchedules,
        ]);
    }

    // Create interview schedule
    public function createInterviewSchedule($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get user
        $user = User::findOrFail($request->user);

        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

        // Get Interview policy
        $interviewPolicy = InterviewPolicy::findOrFail($request->interview_policy);

        // Get department
        $department = Department::findOrFail($request->department);

        // Instance of InterviewSchedule
        $interviewSchedule = new InterviewSchedule();
        $interviewSchedule->interview_policy_id = $interviewPolicy->id;
        $interviewSchedule->user_id = $user->id;
        $interviewSchedule->hiring_request_id = $hiringRequest->id;
        $interviewSchedule->department_id = $department->id;
        $interviewSchedule->date = $request->date;
        $interviewSchedule->time = $request->time;
        $interviewSchedule->location = $request->location;
        $interviewSchedule->interview_type = $request->interview_type;
        $interviewSchedule->application_status = $request->application_status;

        // Save interview schedule
        $practice->interviewSchedules()->save($interviewSchedule);

        // Return success response
        return Response::success([
            'interview-schedule' => $interviewSchedule->with('practice', 'interviewPolicy', 'hiringRequest', 'department.departmentHead.profile', 'user.profile')
                ->latest()
                ->first(),
        ]);

    }
}