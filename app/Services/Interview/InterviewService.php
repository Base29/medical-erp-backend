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
use App\Notifications\Interview\InviteHQStaffNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class InterviewService
{
    // Fetch all of practice's interviews
    public function fetchAllInterviews($request)
    {
        // Init query builder for InterviewSchedule
        $interviewsQuery = InterviewSchedule::query();

        // Check if $request has application_status
        if ($request->has('application_status')) {
            // Fetch interviews filtered by application_status ['first-interview', 'second-interview']
            $interviewsQuery = $interviewsQuery->where('application_status', $request->application_status);
        }

        // Check if $request has is_completed
        if ($request->has('is_completed')) {
            // Fetch interviews filtered by is_completed
            $interviewsQuery = $interviewsQuery->where('is_completed', $request->is_completed);
        }

        // Check if $request has practice
        if ($request->has('practice')) {
            // Fetch interviews filtered by practice
            $interviewsQuery = $interviewsQuery->where('practice_id', $request->practice);
        }

        // Check if $request has department
        if ($request->has('department')) {
            // Fetch interviews filtered by department
            $interviewsQuery = $interviewsQuery->where('department_id', $request->department);
        }

        // Check if $request has interview_type
        if ($request->has('interview_type')) {
            // Fetch interviews filtered by interview_type
            $interviewsQuery = $interviewsQuery->where('interview_type', $request->interview_type);
        }

        // Filtered Interviews
        $interviews = $interviewsQuery->with('practice', 'interviewPolicies.questions.options', 'user.profile', 'hiringRequest')
            ->latest()
            ->paginate(10);

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

        // Init query builder for InterviewSchedule
        $interviewsQuery = InterviewSchedule::query();

        // Check if $request has application_status
        if ($request->has('application_status')) {
            // Fetch interviews filtered by application_status ['first-interview', 'second-interview']
            $interviewsQuery = $interviewsQuery->where('application_status', $request->application_status);
        }

        // Check if $request has is_completed
        if ($request->has('is_completed')) {
            // Fetch interviews filtered by is_completed
            $interviewsQuery = $interviewsQuery->where('is_completed', $request->is_completed);
        }

        // Check if $request has practice
        if ($request->has('practice')) {
            // Fetch interviews filtered by practice
            $interviewsQuery = $interviewsQuery->where('practice_id', $request->practice);
        }

        // Check if $request has department
        if ($request->has('department')) {
            // Fetch interviews filtered by department
            $interviewsQuery = $interviewsQuery->where('department_id', $request->department);
        }

        // Check if $request has interview_type
        if ($request->has('interview_type')) {
            // Fetch interviews filtered by interview_type
            $interviewsQuery = $interviewsQuery->where('interview_type', $request->interview_type);
        }

        $interviewSchedules = $interviewsQuery->where('date', '>', Carbon::now())
            ->with('practice', 'interviewPolicies.questions.options', 'user.profile', 'hiringRequest')
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

        // Check if $user has already 2 interviews
        if ($user->interviewSchedules->count() === 2) {
            throw new \Exception(ResponseMessage::customMessage('User already have first and second interview'));
        }

        // Check if $user doesn't have any interviews. Creating a second interview should not be allowed
        if ($request->application_status === 'second-interview') {
            if ($user->interviewSchedules->isEmpty()) {
                throw new \Exception(ResponseMessage::customMessage('First interview should be conducted before creating the second interview'));
            }
        }

        // Check if the users has a first interview
        if ($request->application_status == 'first-interview') {
            if (!$user->interviewSchedules->isEmpty() && $user->interviewSchedules[0]->application_status === 'first-interview') {
                throw new \Exception(ResponseMessage::customMessage('User already have a first interview. Please schedule a second interview.'));
            }
        }

        // Check if the first interview is completed
        if (!$user->interviewSchedules[0]->is_completed) {
            throw new \Exception(ResponseMessage::customMessage('First interview should be completed before creating second interview'));
        }

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

        // Send notifications to additional staff if invited
        if (!empty($interviewSchedule->additional_staff) && !is_null($interviewSchedule->additional_staff)) {
            // Get info for additional staff
            $additionalStaff = User::findOrFail($interviewSchedule->additional_staff);

            // Notify
            $additionalStaff->notify(new InviteAdditionalStaffNotification(
                $additionalStaff,
                $interviewSchedule,
                $hiringRequest
            ));
        }

        // Send notification to HQ staff if invited
        if (!empty($interviewSchedule->hq_staff) && !is_null($interviewSchedule->hq_staff)) {
            // Get info for hq staff
            $hqStaff = User::findOrFall($interviewSchedule->hq_staff);

            // Notify
            $hqStaff->notify(new InviteHQStaffNotification(
                $hqStaff,
                $hiringRequest,
                $interviewSchedule
            ));

        }

        // Return success response

        // Incase of second interview
        if ($interviewSchedule->application_status === 'second-interview'):

            // Get the id of the first interview
            $firstInterview = $user->interviewSchedules[0]->id;

            // Get misc info first interview
            $firstInterviewMiscInfo = InterviewMiscInfo::where('interview', $firstInterview)->firstOrFail();

            // Get first interview score
            $firstInterviewScore = InterviewScore::where('interview', $firstInterview)->firstOrFail();

            // Cast $interviewSchedule to $secondInterviewSchedule variable
            $secondInterviewSchedule = $interviewSchedule->with('practice', 'interviewPolicies.questions.options', 'hiringRequest', 'department.departmentHead.profile', 'user.profile')
                ->latest()
                ->first();

            // Converting to array
            $secondInterviewScheduleWithAdditionalData = $secondInterviewSchedule->toArray();

            // Inserting additional data from the first interview
            $secondInterviewScheduleWithAdditionalData['first_interview_data']['misc_info'] = $firstInterviewMiscInfo;
            $secondInterviewScheduleWithAdditionalData['first_interview_data']['score'] = $firstInterviewScore;

            return Response::success([
                'interview-schedule' => $secondInterviewScheduleWithAdditionalData,
            ]);

        else:
            // Incase of first interview
            return Response::success([
                'interview-schedule' => $interviewSchedule->with('practice', 'interviewPolicies.questions.options', 'hiringRequest', 'department.departmentHead.profile', 'user.profile')
                    ->latest()
                    ->first(),
            ]);
        endif;
    }

    // Update interview
    public function updateInterviewSchedule($request)
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

        // Init query builder for InterviewSchedule
        $interviewsQuery = InterviewSchedule::query();

        // Check if $request has application_status
        if ($request->has('application_status')) {
            // Fetch interviews filtered by application_status ['first-interview', 'second-interview']
            $interviewsQuery = $interviewsQuery->where('application_status', $request->application_status);
        }

        // Check if $request has is_completed
        if ($request->has('is_completed')) {
            // Fetch interviews filtered by is_completed
            $interviewsQuery = $interviewsQuery->where('is_completed', $request->is_completed);
        }

        // Check if $request has practice
        if ($request->has('practice')) {
            // Fetch interviews filtered by practice
            $interviewsQuery = $interviewsQuery->where('practice_id', $request->practice);
        }

        // Check if $request has department
        if ($request->has('department')) {
            // Fetch interviews filtered by department
            $interviewsQuery = $interviewsQuery->where('department_id', $request->department);
        }

        // Check if $request has interview_type
        if ($request->has('interview_type')) {
            // Fetch interviews filtered by interview_type
            $interviewsQuery = $interviewsQuery->where('interview_type', $request->interview_type);
        }

        // Get past interview schedules
        $interviewSchedules = $interviewsQuery->where('is_completed', 1)
            ->with('practice', 'interviewPolicies.questions.options', 'user.profile', 'hiringRequest')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'interview-schedules' => $interviewSchedules,
        ]);
    }

    // Store interview answers
    public function storeInterviewAnswer($request)
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

    public function createAdhocQuestions($request)
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
    public function createCandidateQuestions($request)
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
            $candidateQuestion->answer = $question['answer'];
            $candidateQuestion->save();
        }

        return Response::success([
            'message' => ResponseMessage::customMessage('Candidate Questions saved'),
        ]);
    }

    // Fetch single interview
    public function fetchSingleInterview($request)
    {
        // Get interview with $request->user answers
        $interviewSchedule = InterviewSchedule::where('id', $request->interview)
            ->with([
                'user.profile',
                'user.education',
                'user.employmentHistories',
                'hiringRequest',
                'interviewPolicies.questions.options',
                'interviewPolicies.questions.interviewAnswers' => function ($q) use ($request) {
                    $q->where('interview', $request->interview);
                },
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
    public function fetchAdhocQuestions($request)
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
    public function fetchCandidateQuestions($request)
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
    public function deleteAdhocQuestion($request)
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
    public function deleteCandidateQuestion($request)
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
    public function addMiscInfo($request)
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
    public function createInterviewScore($request)
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
    public function getAllInterviews($request)
    {
        // Init query builder for InterviewSchedule
        $interviewsQuery = InterviewSchedule::query();

        // Check if $request has application_status
        if ($request->has('application_status')) {
            // Fetch interviews filtered by application_status ['first-interview', 'second-interview']
            $interviewsQuery = $interviewsQuery->where('application_status', $request->application_status);
        }

        // Check if $request has is_completed
        if ($request->has('is_completed')) {
            // Fetch interviews filtered by is_completed
            $interviewsQuery = $interviewsQuery->where('is_completed', $request->is_completed);
        }

        // Check if $request has practice
        if ($request->has('practice')) {
            // Fetch interviews filtered by practice
            $interviewsQuery = $interviewsQuery->where('practice_id', $request->practice);
        }

        // Check if $request has department
        if ($request->has('department')) {
            // Fetch interviews filtered by department
            $interviewsQuery = $interviewsQuery->where('department_id', $request->department);
        }

        // Check if $request has interview_type
        if ($request->has('interview_type')) {
            // Fetch interviews filtered by interview_type
            $interviewsQuery = $interviewsQuery->where('interview_type', $request->interview_type);
        }

        // Get all interviews
        $interviews = $interviewsQuery->with('practice', 'interviewPolicies.questions.options', 'user.profile', 'hiringRequest')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'interviews' => $interviews,
        ]);
    }

    // Processing count by contract type
    public function processCount($column, $value)
    {
        return InterviewSchedule::whereHas('hiringRequest', function ($q) use ($column, $value) {
            $q->where($column, $value);
        })->count();
    }
}