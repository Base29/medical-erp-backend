<?php

namespace App\Services\Interview;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\AdhocQuestion;
use App\Models\CandidateQuestion;
use App\Models\Department;
use App\Models\HiringRequest;
use App\Models\Interview;
use App\Models\InterviewAnswer;
use App\Models\InterviewMiscInfo;
use App\Models\InterviewPolicy;
use App\Models\InterviewQuestion;
use App\Models\InterviewSchedule;
use App\Models\InterviewScore;
use App\Models\Practice;
use App\Models\User;
use App\Notifications\Interview\InviteAdditionalStaffNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
                ->with('practice', 'interviewPolicies.questions.options', 'user.profile', 'hiringRequest')
                ->latest()
                ->paginate(10);

        } else {
            // Get $practice interviews
            $interviews = InterviewSchedule::with('practice', 'interviewPolicies.questions.options', 'user.profile', 'hiringRequest')
                ->latest()
                ->paginate(10);
        }

        /**
         * Count according to contract type
         */

        // Getting count of permanent contract
        $permanent = $this->processCount('contract_type', 'permanent');

        // Getting count of fixed term contract
        $fixedTerm = $this->processCount('contract_type', 'fixed-term');

        // Getting count of casual contract
        $casual = $this->processCount('contract_type', 'casual');

        // Getting count of zero hour contract
        $zeroHour = $this->processCount('contract_type', 'zero-hour');

        $countByContractType = collect(['count' => [
            'permanent' => $permanent,
            'fixed-term' => $fixedTerm,
            'casual' => $casual,
            'zero-hour' => $zeroHour,
        ]]);

        $interviewsWithCount = $countByContractType->merge($interviews);

        // Return success response
        return Response::success([
            'interviews' => $interviewsWithCount,
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
                ->with('practice', 'interviewPolicies.questions.options', 'user.profile', 'hiringRequest')
                ->latest()
                ->paginate(10);
        } else {
            // Get $practice interview schedules
            $interviewSchedules = InterviewSchedule::where('date', '>', Carbon::now())
                ->with('practice', 'interviewPolicies.questions.options', 'user.profile', 'hiringRequest')
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
        $interviewPolicy = InterviewPolicy::where('role_id', $user->roles[0]->id)->first();

        if (!$interviewPolicy) {
            throw new \Exception(ResponseMessage::customMessage('No interview policy associated with role ' . $user->roles[0]->name));
        }

        // Get department
        $department = Department::findOrFail($request->department);

        // Instance of InterviewSchedule
        $interviewSchedule = new InterviewSchedule();
        $interviewSchedule->user_id = $user->id;
        $interviewSchedule->hiring_request_id = $hiringRequest->id;
        $interviewSchedule->department_id = $department->id;
        $interviewSchedule->date = $request->date;
        $interviewSchedule->time = $request->time;
        $interviewSchedule->location = $request->location;
        $interviewSchedule->duration = $request->duration;
        $interviewSchedule->interview_type = $request->interview_type;
        $interviewSchedule->application_status = $request->application_status;
        $interviewSchedule->additional_staff = $request->additional_staff;
        $interviewSchedule->hq_staff = $request->hq_staff;

        // Save interview schedule
        $practice->interviewSchedules()->save($interviewSchedule);

        // Attach Interview Policy
        $interviewSchedule->interviewPolicies()->attach($interviewPolicy->id);

        // Send notification to additional staff
        if (!empty($interviewSchedule->additional_staff) && is_null($interviewSchedule->additional_staff)) {
            // Get data for additional staff
            $additionalStaff = User::findOrFail($interviewSchedule->additional_staff);

            $additionalStaff->notify(new InviteAdditionalStaffNotification(
                $additionalStaff, 
                $interviewSchedule, 
                $hiringRequest
            ));
        }

        // Return success response {
        return Response::success([
            'interview-schedule' => $interviewSchedule->with('practice', 'interviewPolicies.questions.options', 'hiringRequest', 'department.departmentHead.profile', 'user.profile')
                ->latest()
                ->first(),
        ]);
    }

}

// Update interview
function updateInterviewSchedule($request)
{
    // Get interview schedule
    $interviewSchedule = InterviewSchedule::findOrFail($request->interview);

    // // Update is_completed field for $interviewSchedule
    // $interviewSchedule->is_completed = $request->is_completed;
    // $interviewSchedule->save();

    UpdateService::updateModel($interviewSchedule, $request->validated(), 'interview');

    // Return success response
    return Response::success([
        'interview' => $interviewSchedule->with('practice', 'department.departmentHead.profile', 'user.profile', 'hiringRequest')
            ->latest('updated_at')
            ->first(),
    ]);
}

// Delete interview schedule
function deleteInterviewSchedule($request)
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
function fetchPastInterviews($request)
{

    // Get past interview schedules
    $interviewSchedules = InterviewSchedule::where('is_completed', 1)
        ->with('practice', 'interviewPolicies.questions.options', 'user.profile', 'hiringRequest')
        ->latest()
        ->paginate(10);

    // Return success response
    return Response::success([
        'interview-schedules' => $interviewSchedules,
    ]);
}

// Store interview answers
function storeInterviewAnswer($request)
{
    // Get interview schedule
    $interviewSchedule = InterviewSchedule::findOrFail($request->interview);

    // Get interview question
    $interviewQuestion = InterviewQuestion::findOrFail($request->question);

    /**
     * Check type of the $interviewQuestion.
     *
     * If the type = descriptive then save the text in the answer field of the DB table.
     * Or if the type = single-choice/multi-choice then save the ID of the option in the option field of the DB table.
     *
     * In-case of the type multi-choice loop through the options provided by the frontend and save them separately in the option field of the DB
     * table.
     */

    // Get type of $interviewQuestion
    $questionType = $interviewQuestion->type;

    switch ($questionType) {
        case 'descriptive':
            if (!$request->has('answer')) {
                throw new \Exception(ResponseMessage::customMessage('Answer to question type descriptive require answer key to be sent in request'));
            }

            if (Str::of($request->answer)->isEmpty) {
                throw new \Exception(ResponseMessage::customMessage('answer key should not be empty'));
            }

            // Initiate instance of InterviewAnswer model
            $interviewAnswer = new InterviewAnswer();

            $interviewAnswer->interview = $interviewSchedule->id;
            $interviewAnswer->user = $interviewSchedule->user_id;
            $interviewAnswer->question = $interviewQuestion->id;
            $interviewAnswer->answer = $request->answer;
            $interviewAnswer->save();

            return Response::success([
                'message' => 'Answer Saved',
            ]);

            break;
        case 'single-choice':
            if (!$request->has('option')) {
                throw new \Exception(ResponseMessage::customMessage('Answer to question type single-choice require option key to be sent in request'));
            }

            // Initiate instance of InterviewAnswer model
            $interviewAnswer = new InterviewAnswer();

            $interviewAnswer->interview = $interviewSchedule->id;
            $interviewAnswer->user = $interviewSchedule->user_id;
            $interviewAnswer->question = $interviewQuestion->id;
            $interviewAnswer->option = $request->option;
            $interviewAnswer->save();

            return Response::success([
                'message' => 'Answer Saved',
            ]);

            break;
        case 'multi-choice':
            if (!$request->has('options')) {
                throw new \Exception(ResponseMessage::customMessage('Answer to question type multi-choice require options array key to be sent in request'));
            }

            // Cast $request->options to $options
            $options = $request->options;

            // Loop through $request->assert_options
            foreach ($options as $option) {

                // Initiate instance of InterviewAnswer model
                $interviewAnswer = new InterviewAnswer();

                $interviewAnswer->interview = $interviewSchedule->id;
                $interviewAnswer->user = $interviewSchedule->user_id;
                $interviewAnswer->question = $interviewQuestion->id;
                $interviewAnswer->option = $option;
                $interviewAnswer->save();
            }

            return Response::success([
                'message' => 'Answer Saved',
            ]);

            break;
        default:
            return true;
    }
}

function createAdhocQuestions($request)
{
    // Get interview schedule
    $interviewSchedule = InterviewSchedule::findOrFail($request->interview);

    // Cast $request->questions to variable $questions
    $questions = $request->questions;

    // Loop through $request->questions
    foreach ($questions as $question) {
        // Initiate instance of AdhocQuestion model
        $adhocQuestion = new AdhocQuestion();
        $adhocQuestion->interview = $interviewSchedule->id;
        $adhocQuestion->question = $question['question'];
        $adhocQuestion->answer = $question['answer'];
        $adhocQuestion->save();
    }

    return Response::success([
        'message' => ResponseMessage::customMessage('Adhoc Questions saved'),
    ]);
}

// Create candidate questions
function createCandidateQuestions($request)
{
    // Get interview schedule
    $interviewSchedule = InterviewSchedule::findOrFail($request->interview);

    // Cast $request->questions to variable $questions
    $questions = $request->questions;

    // Loop through $request->questions
    foreach ($questions as $question) {
        // Initiate instance of AdhocQuestion model
        $candidateQuestion = new CandidateQuestion();
        $candidateQuestion->interview = $interviewSchedule->id;
        $candidateQuestion->question = $question['question'];
        $candidateQuestion->save();
    }

    return Response::success([
        'message' => ResponseMessage::customMessage('Candidate Questions saved'),
    ]);
}

// Fetch single interview
function fetchSingleInterview($request)
{
    // Get interview with $request->user answers
    $interviewSchedule = InterviewSchedule::where('id', $request->interview)
        ->with([
            'user.profile',
            'hiringRequest',
            'interviewPolicies.questions.options',
            'interviewPolicies.questions.interviewAnswers',
            'practice',
            'candidateQuestions',
            'adhocQuestions',
            'interviewMiscInfo',
            'interviewScore',
        ])
        ->firstOrFail();

    // Return success response
    return Response::success([
        'interview' => $interviewSchedule,
    ]);
}

// Fetch adhoc questions
function fetchAdhocQuestions($request)
{
    // Get interview schedule
    $interviewSchedule = InterviewSchedule::findOrFail($request->interview);

    // AdhocQuestions
    $adhocQuestions = AdhocQuestion::where('interview', $interviewSchedule->id)->latest()->get();

    // Return success response
    return Response::success([
        'adhoc-questions' => $adhocQuestions,
    ]);
}

// Fetch candidate questions
function fetchCandidateQuestions($request)
{
    // Get interview schedule
    $interviewSchedule = InterviewSchedule::findOrFail($request->interview);

    // AdhocQuestions
    $candidateQuestions = CandidateQuestion::where('interview', $interviewSchedule->id)->latest()->get();

    // Return success response
    return Response::success([
        'candidate-questions' => $candidateQuestions,
    ]);
}

// Delete adhoc question
function deleteAdhocQuestion($request)
{
    // Get adhoc question
    $adhocQuestion = AdhocQuestion::findOrFail($request->adhoc_question);

    // Delete adhoc question
    $adhocQuestion->delete();

    // Return success response
    return Response::success([
        'adhoc-question' => $adhocQuestion,
    ]);
}

// Delete candidate question
function deleteCandidateQuestion($request)
{
    // Get adhoc question
    $candidateQuestion = CandidateQuestion::findOrFail($request->candidate_question);

    // Delete adhoc question
    $candidateQuestion->delete();

    // Return success response
    return Response::success([
        'candidate-question' => $candidateQuestion,
    ]);
}

// Add interview misc info
function addMiscInfo($request)
{
    // Get interview
    $interviewSchedule = InterviewSchedule::findOrFail($request->interview);

    // Initiate a instance of InterviewMiscInfo
    $interviewMiscInfo = new InterviewMiscInfo();
    $interviewMiscInfo->interview = $interviewSchedule->id;
    $interviewMiscInfo->current_salary = $request->current_salary;
    $interviewMiscInfo->expected_salary = $request->expected_salary;
    $interviewMiscInfo->difference = $request->difference;
    $interviewMiscInfo->availability = $request->availability;
    $interviewMiscInfo->available_time = $request->available_time;
    $interviewMiscInfo->job_type = $request->job_type;
    $interviewMiscInfo->dbs = $request->dbs;
    $interviewMiscInfo->dismissals = $request->dismissals;
    $interviewMiscInfo->given_notice = $request->given_notice;
    $interviewMiscInfo->notice_start = $request->notice_start;
    $interviewMiscInfo->notice_duration = $request->notice_duration;
    $interviewMiscInfo->interviewing_elsewhere = $request->interviewing_elsewhere;
    $interviewMiscInfo->salary_notes = $request->salary_notes;
    $interviewMiscInfo->notice_notes = $request->notice_notes;
    $interviewMiscInfo->save();

    // Return success response
    return Response::success([
        'interview-misc-info' => $interviewMiscInfo,
    ]);
}

// Create interview score
function createInterviewScore($request)
{
    // Get interview
    $interviewSchedule = InterviewSchedule::findOrFail($request->interview);

    // Initiate instance of InterviewScore
    $interviewScore = new InterviewScore();
    $interviewScore->interview = $interviewSchedule->id;
    $interviewScore->cultural_fit = $request->cultural_fit;
    $interviewScore->career_motivation = $request->career_motivation;
    $interviewScore->social_skills = $request->social_skills;
    $interviewScore->team_work = $request->team_work;
    $interviewScore->technical_skills = $request->technical_skills;
    $interviewScore->leadership_capability = $request->leadership_capability;
    $interviewScore->critical_thinking_problem_solving = $request->critical_thinking_problem_solving;
    $interviewScore->self_awareness = $request->self_awareness;
    $interviewScore->total = $request->total;
    $interviewScore->remarks = $request->remarks;

    // Save interview score
    $interviewScore->save();

    // Return success response
    return Response::success([
        'interview-score' => $interviewScore,
    ]);
}

// Get All interviews
function getAllInterviews()
{
    // Get all interviews
    $interviews = InterviewSchedule::with('practice', 'interviewPolicies.questions.options', 'user.profile', 'hiringRequest')
        ->latest()
        ->paginate(10);

    // Return success response
    return Response::success([
        'interviews' => $interviews,
    ]);
}

// Processing count by contract type
function processCount($column, $value)
{
    return InterviewSchedule::whereHas('hiringRequest', function ($q) use ($column, $value) {
        $q->where($column, $value);
    })->count();
}
}