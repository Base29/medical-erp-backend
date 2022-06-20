<?php
namespace App\Services\Interview;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
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
    public function fetchAllInterviews($request)
    {
        if (!$request->is('api/hq/*')) {

            // Check if the practice id is provided
            if (!$request->has('practice')) {
                throw new \Exception(ResponseMessage::customMessage('practice field is required.'));
            }

            // Get practice
            $practice = Practice::findOrFail($request->practice);

            // Get $practice interviews
            $interviews = InterviewSchedule::where('practice_id', $practice->id)
                ->with('practice', 'interviewPolicy', 'user.profile', 'hiringRequest')
                ->latest()
                ->paginate(10);

        } else {
            // Get $practice interviews
            $interviews = InterviewSchedule::with('practice', 'interviewPolicy', 'user.profile', 'hiringRequest')
                ->latest()
                ->paginate(10);
        }

        // Return success response
        return Response::success([
            'interviews' => $interviews,
        ]);
    }

    // Fetch interview schedules for a practice
    public function fetchUpcomingInterviewSchedules($request)
    {
        if (!$request->is('api/hq/*')) {
            // Check if the practice id is provided
            if (!$request->has('practice')) {
                throw new \Exception(ResponseMessage::customMessage('practice field is required.'));
            }

            // Get practice
            $practice = Practice::findOrFail($request->practice);

            // Get $practice interview schedules
            $interviewSchedules = InterviewSchedule::where('practice_id', $practice->id)
                ->where('date', '>', Carbon::now())
                ->with('practice', 'interviewPolicy', 'user.profile', 'hiringRequest')
                ->latest()
                ->paginate(10);
        } else {
            // Get $practice interview schedules
            $interviewSchedules = InterviewSchedule::where('date', '>', Carbon::now())
                ->with('practice', 'interviewPolicy', 'user.profile', 'hiringRequest')
                ->latest()
                ->paginate(10);
        }

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
        $interviewPolicy = InterviewPolicy::where('role_id', $user->roles[0]->id)->firstOrFail();

        if (!$interviewPolicy) {
            throw new \Exception(ResponseMessage::customMessage('No interview policy associated with role ' . $user->roles[0]->id));
        }

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
        $interviewSchedule->additional_staff = $request->additional_staff;
        $interviewSchedule->hq_staff = $request->hq_staff;

        // Save interview schedule
        $practice->interviewSchedules()->save($interviewSchedule);

        // Return success response
        return Response::success([
            'interview-schedule' => $interviewSchedule->with('practice', 'interviewPolicy', 'hiringRequest', 'department.departmentHead.profile', 'user.profile')
                ->latest()
                ->first(),
        ]);

    }

    // Update interview
    public function updateInterviewSchedule($request)
    {
        // Get interview schedule
        $interviewSchedule = InterviewSchedule::findOrFail($request->interview);

        // Update is_completed field for $interviewSchedule
        $interviewSchedule->is_completed = $request->is_completed;
        $interviewSchedule->save();

        // Return success response
        return Response::success([
            'interview' => $interviewSchedule->with('practice', 'department.departmentHead.profile', 'user.profile', 'hiringRequest')
                ->latest('updated_at')
                ->first(),
        ]);
    }

    // Delete interview schedule
    public function deleteInterviewSchedule($request)
    {
        // Get interview schedule
        $interviewSchedule = InterviewSchedule::findOrFail($request->id);

        // Delete interview schedule
        $interviewSchedule->delete();

        // Return success response
        return Response::success([
            'message' => ResponseMessage::deleteSuccess('Interview Schedule'),
        ]);
    }

    // Fetch past interviews
    public function fetchPastInterviews($request)
    {

        // Get past interview schedules
        $interviewSchedules = InterviewSchedule::where('date', '<', Carbon::now())
            ->with('practice', 'interviewPolicy', 'user.profile', 'hiringRequest')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'interview-schedules' => $interviewSchedules,
        ]);
    }
}