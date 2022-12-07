<?php

namespace App\Services\Appraisal;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\Appraisal;
use App\Models\AppraisalAnswer;
use App\Models\AppraisalPolicy;
use App\Models\AppraisalQuestion;
use App\Models\AppraisalQuestionOption;
use App\Models\AppraisalReschedule;
use App\Models\Department;
use App\Models\Practice;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class AppraisalService
{
    public function createAppraisal($request)
    {
        $practice = Practice::findOrFail($request->practice);

        // Get user
        $user = User::findOrFail($request->user);

        // Get appraisal policy
        $appraisalPolicy = AppraisalPolicy::where('role', $user->roles[0]->id)->first();

        if (!$appraisalPolicy) {
            throw new Exception(
                ResponseMessage::customMessage('No appraisal policy associated with role ' . $user->roles[0]->name),
                Response::HTTP_NOT_FOUND
            );
        }

        // Get department
        $department = Department::findOrFail($request->department);

        // Instance of Appraisal
        $appraisal = new Appraisal();
        $appraisal->user = $user->id;
        $appraisal->department = $department->id;
        $appraisal->practice = $practice->id;
        $appraisal->date = $request->date;
        $appraisal->time = $request->time;
        $appraisal->location = $request->location;
        $appraisal->type = $request->type;
        $appraisal->status = $request->status;
        $appraisal->additional_staff = $request->additional_staff;
        $appraisal->is_completed = Config::get('constants.APPRAISAL.INCOMPLETE');
        $appraisal->hq_staff = $request->hq_staff;

        // Save Appraisal schedule
        $appraisal->save();

        // Attach Appraisal Policy
        $appraisal->appraisalPolicies()->attach($appraisalPolicy->id);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_CREATED,
            'appraisal' => $appraisal->with('practice', 'appraisalPolicies.questions.options', 'department.departmentHead.profile', 'user.profile')
                ->latest()
                ->first(),
        ]);
    }

    // Create appraisal policy
    public function createAppraisalPolicy($request)
    {
        // Get role
        $role = Role::findOrFail($request->role);

        if ($role->hasAppraisalPolicy()) {
            throw new Exception(
                ResponseMessage::customMessage('Role ' . $role->name . ' already have a appraisal policy'),
                Response::HTTP_CONFLICT
            );
        }

        // Instance of AppraisalPolicy
        $appraisalPolicy = new AppraisalPolicy();
        $appraisalPolicy->role = $role->id;
        $appraisalPolicy->name = $request->name;
        $appraisalPolicy->save();

        // Save questions for $interviewPolicy
        $this->saveQuestions($request->questions, $appraisalPolicy->id);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_CREATED,
            'appraisal-policy' => $appraisalPolicy->with('questions.options')
                ->latest()
                ->first(),
        ]);

    }

    // Save questions
    public function saveQuestions($questions, $appraisalPolicyId)
    {
        // Iterate through $questions array
        foreach ($questions as $question) {

            // Instance of InterviewQuestion
            $appraisalQuestion = new AppraisalQuestion();
            $appraisalQuestion->policy = $appraisalPolicyId;
            $appraisalQuestion->type = $question['type'];
            $appraisalQuestion->head = $question['head'];
            $appraisalQuestion->question = $question['question'];

            // Save question
            $appraisalQuestion->save();

            // Check if $interviewQuestion is multi-choice or single-choice
            if ($appraisalQuestion->type === 'multi-choice' || $appraisalQuestion->type === 'single-choice') {
                // Save options for $interviewQuestion
                $this->saveOptions($question['options'], $appraisalQuestion->id);
            }

        }
    }

    // Save options for questions
    public function saveOptions($options, $appraisalQuestionId)
    {
        // Iterate through $options array
        foreach ($options as $option) {
            // Instance of InterviewQuestionOption
            $appraisalQuestionOption = new AppraisalQuestionOption();
            $appraisalQuestionOption->question = $appraisalQuestionId;
            $appraisalQuestionOption->option = $option['option'];

            // Save InterviewQuestionOption
            $appraisalQuestionOption->save();
        }
    }

    // Fetch all interview policies belongs to a practice
    public function fetchAllAppraisalPolicies()
    {

        // Get all interview policies
        $appraisalPolicies = AppraisalPolicy::with('role', 'questions.options')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'appraisal-policies' => $appraisalPolicies,
        ]);

    }

    // Fetch interview policies for a practice
    public function fetchPracticeAppraisalPolicies($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get all interview policies of $practice
        $appraisalPolicies = AppraisalPolicy::where('practice', $practice->id)
            ->with('questions.options', 'practice', 'role')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'appraisal-policies' => $appraisalPolicies,
        ]);
    }

    // Fetch single interview policy
    public function fetchSingleAppraisalPolicy($request)
    {
        // Get interview policy
        $appraisalPolicy = AppraisalPolicy::where('id', $request->interview_policy)
            ->with('questions.options', 'practice', 'role')
            ->firstOrFail();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'appraisal-policy' => $appraisalPolicy,
        ]);
    }

    // Delete interview policy
    public function deleteAppraisalPolicy($request)
    {
        // Get interview policy
        $appraisalPolicy = AppraisalPolicy::findOrFail($request->appraisal_policy);

        // Delete interview policy
        $appraisalPolicy->delete();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'appraisal-policy' => $appraisalPolicy,
        ]);
    }

    // Update interview policy
    public function updateAppraisalPolicy($request)
    {

        // Allowed Fields
        $allowedFields = [
            'name',
            'role',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get interview policy
        $appraisalPolicy = AppraisalPolicy::findOrFail($request->appraisal_policy);

        // Update interview policy
        $appraisalPolicyUpdated = UpdateService::updateModel($appraisalPolicy, $request->validated(), 'appraisal_policy');

        // Return response if update fails
        if (!$appraisalPolicyUpdated) {
            throw new Exception(
                ResponseMessage::customMessage('Something went wrong. Cannot update Appraisal Policy'),
                Response::HTTP_BAD_REQUEST
            );
        }

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'appraisal-policy' => $appraisalPolicy->with('role', 'questions.options')
                ->latest('updated_at')
                ->first(),
        ]);
    }

    // Update interview policy questions
    public function updateQuestion($request)
    {

        // Allowed fields
        $allowedFields = [
            'question',
            'type',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get interview question
        $appraisalQuestion = AppraisalQuestion::findOrFail($request->question_id);

        // Update subitem
        UpdateService::updateModel($appraisalQuestion, $request->validated(), 'question');

        // Check if the type of the question is updated to descriptive
        if ($appraisalQuestion->type === 'descriptive') {
            // Get options
            $options = AppraisalQuestionOption::where('appraisal_question', $appraisalQuestion->id);
            $options->delete();
        }

        // Return success response
        return Response::success([
            'appraisal-question' => $appraisalQuestion->with('options')->latest('updated_at')->first(),
        ]);

    }

    // Update options for questions
    public function updateOptions($options)
    {
        // Iterate through $options array
        foreach ($options as $option) {
            // Instance of InterviewQuestionOption
            $appraisalQuestionOption = AppraisalQuestionOption::findOrFail($option['id']);

            // Update InterviewQuestionOption
            UpdateService::updateModel($appraisalQuestionOption, $option, 'id');
        }
    }

    // Fetch all of practice's appraisals
    public function fetchAllAppraisals($request)
    {
        // Query builder for appraisals
        $appraisalsQuery = Appraisal::query();

        // If $request has practice
        if ($request->has('practice')) {
            $appraisalsQuery = $appraisalsQuery->where('practice', $request->practice);
        }

        // If $request has is_completed
        if ($request->has('is_completed')) {
            $appraisalsQuery = $appraisalsQuery->where('is_completed', $request->is_completed);
        }

        // Return appraisals
        $appraisals = $appraisalsQuery->with('practice', 'appraisalPolicies.questions.options', 'user.profile')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'appraisals' => $appraisals,
        ]);
    }

    // Fetch upcoming appraisals for a practice
    public function fetchUpcomingAppraisals($request)
    {
        if (!$request->is('api/hq/*')) {
            // Check if the practice id is provided
            if (!$request->has('practice')) {
                throw new Exception(
                    ResponseMessage::customMessage('practice field is required.'),
                    Response::HTTP_BAD_REQUEST
                );
            }

            // Get practice
            $practice = Practice::findOrFail($request->practice);

            // Get $practice appraisals
            $appraisals = Appraisal::where('practice', $practice->id)
                ->where('date', '>', Carbon::now())
                ->with('practice', 'appraisalPolicies.questions.options', 'user.profile')
                ->latest()
                ->paginate(10);
        } else {
            // Get $practice appraisals
            $appraisals = Appraisal::where('date', '>', Carbon::now())
                ->with('practice', 'appraisalPolicies.questions.options', 'user.profile')
                ->latest()
                ->paginate(10);
        }

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'upcoming-appraisals' => $appraisals,
        ]);
    }

    // Update appraisal
    public function updateAppraisal($request)
    {
        // Get Appraisal
        $appraisal = Appraisal::findOrFail($request->appraisal);

        UpdateService::updateModel($appraisal, $request->validated(), 'appraisal');

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'appraisal' => $appraisal->with('practice', 'department.departmentHead.profile', 'user.profile')
                ->latest('updated_at')
                ->first(),
        ]);
    }

    // Delete appraisal
    public function deleteAppraisal($request)
    {
        // Get appraisal
        $appraisal = Appraisal::findOrFail($request->id);

        // Delete appraisal
        $appraisal->delete();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'appraisal' => $appraisal,
        ]);
    }

    // Fetch completed appraisals
    public function fetchCompletedAppraisals($request)
    {

        // Get past interview schedules
        $appraisals = Appraisal::where('is_completed', 1)
            ->with('practice', 'appraisalPolicies.questions.options', 'user.profile')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'completed-appraisals' => $appraisals,
        ]);
    }

    // Store appraisal answers
    public function storeAppraisalAnswer($request)
    {
        // Get appraisal
        $appraisal = Appraisal::findOrFail($request->appraisal);

        // Get appraisal question
        $appraisalQuestion = AppraisalQuestion::findOrFail($request->question);

        /**
         * Check type of the $appraisalQuestion.
         *
         * If the type = descriptive then save the text in the answer field of the DB table.
         * Or if the type = single-choice/multi-choice then save the ID of the option in the option field of the DB table.
         *
         * In-case of the type multi-choice loop through the options provided by the frontend and save them separately in the option field of the DB
         * table.
         */

        // Get type of $appraisalQuestion
        $questionType = $appraisalQuestion->type;

        switch ($questionType) {
            case 'descriptive':
                if (!$request->has('answer')) {
                    throw new Exception(
                        ResponseMessage::customMessage('Answer to question type descriptive require answer key to be sent in request'),
                        Response::HTTP_BAD_REQUEST
                    );
                }

                if (Str::of($request->answer)->isEmpty) {
                    throw new Exception(
                        ResponseMessage::customMessage('answer key should not be empty'),
                        Response::HTTP_BAD_REQUEST
                    );
                }

                // Initiate instance of InterviewAnswer model
                $appraisalAnswer = new AppraisalAnswer();

                $appraisalAnswer->appraisal = $appraisal->id;
                $appraisalAnswer->question = $appraisalQuestion->id;
                $appraisalAnswer->answer = $request->answer;
                $appraisalAnswer->user = $appraisal->user;
                $appraisalAnswer->save();

                return Response::success([
                    'code' => Response::HTTP_CREATED,
                    'answer' => $appraisalAnswer,
                ]);

                break;
            case 'single-choice':
                if (!$request->has('option')) {
                    throw new Exception(
                        ResponseMessage::customMessage('Answer to question type single-choice require option key to be sent in request'),
                        Response::HTTP_BAD_REQUEST
                    );
                }

                // Check if $options array contains non associated option
                if (!$appraisalQuestion->optionNotAssociatedWithQuestion($request->option, $appraisalQuestion->type)) {
                    throw new Exception(ResponseMessage::customMessage('The option ' . $request->option . ' is not associated with question ' . $appraisalQuestion->id));
                }

                // Fetch the answer string for the option id
                $questionOption = AppraisalQuestionOption::findOrFail($request->option);

                // Initiate instance of InterviewAnswer model
                $appraisalAnswer = new AppraisalAnswer();

                $appraisalAnswer->appraisal = $appraisal->id;
                $appraisalAnswer->question = $appraisalQuestion->id;
                $appraisalAnswer->option = $request->option;
                $appraisalAnswer->answer = $questionOption->option;
                $appraisalAnswer->user = $appraisal->user;
                $appraisalAnswer->save();

                return Response::success([
                    'code' => Response::HTTP_CREATED,
                    'answer' => $appraisalAnswer,
                ]);

                break;
            case 'multi-choice':
                if (!$request->has('options')) {
                    throw new Exception(
                        ResponseMessage::customMessage('Answer to question type multi-choice require options array key to be sent in request'),
                        Response::HTTP_BAD_REQUEST
                    );
                }

                // Cast $request->options to $options
                $options = $request->options;

                // Cast output of method optionNotAssociatedWithQuestion to variable
                $optionNotAssociatedWithQuestion = $appraisalQuestion->optionNotAssociatedWithQuestion($options, $appraisalQuestion->type);

                // Check if $options array contains non associated option
                if (!is_null($optionNotAssociatedWithQuestion)) {
                    // Casting to variable
                    $nonAssociatedOption = implode(', ', $optionNotAssociatedWithQuestion);

                    // Getting count of the invalid options
                    $optionCount = count(explode(', ', $nonAssociatedOption));

                    // Building test according the $optionCount
                    $optionText = $optionCount > 1 ? 'options ' . $nonAssociatedOption . ' are' : 'option ' . $nonAssociatedOption . ' is';

                    throw new Exception(ResponseMessage::customMessage('The ' . $optionText . ' not associated with question ' . $appraisalQuestion->id));
                }

                // Loop through $request->assert_options
                foreach ($options as $option) {

                    // Fetch the answer string for the option id
                    $questionOption = AppraisalQuestionOption::findOrFail($option);

                    // Initiate instance of InterviewAnswer model
                    $appraisalAnswer = new AppraisalAnswer();

                    $appraisalAnswer->appraisal = $appraisal->id;
                    $appraisalAnswer->question = $appraisalQuestion->id;
                    $appraisalAnswer->option = $option;
                    $appraisalAnswer->answer = $questionOption->option;
                    $appraisalAnswer->user = $appraisal->user;
                    $appraisalAnswer->save();
                }

                return Response::success([
                    'code' => Response::HTTP_CREATED,
                    'answer' => $appraisalAnswer,
                ]);

                break;
            default:
                return true;
        }
    }

    // Fetch single appraisal
    public function fetchSingleAppraisal($request)
    {
        // Get interview schedule
        $appraisal = Appraisal::where('id', $request->appraisal)
            ->with([
                'user.profile',
                'user.workPatterns.workTimings',
                'user.department.departmentHead.profile',
                'user.objectives',
                'appraisalPolicies.questions.options',
                'appraisalPolicies.questions.appraisalAnswers' => function ($q) use ($request) {
                    $q->where('appraisal', $request->appraisal);
                },
                'practice.practiceManager.profile',
            ])
            ->firstOrFail();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'appraisal' => $appraisal,
        ]);
    }

    // Fetch role appraisal policies belongs to a practice
    public function fetchRoleAppraisalPolicy($request)
    {

        // Get Role
        $role = Role::findOrFail($request->role);

        // Get all interview policies
        $appraisalPolicy = AppraisalPolicy::where('role', $role->id)->with('role', 'questions.options')
            ->firstOrFail();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'appraisal-policy' => $appraisalPolicy,
        ]);

    }

    // Appraisal conclusion
    public function appraisalConclusion($request)
    {
        // Get appraisal
        $appraisal = Appraisal::findOrFail($request->appraisal);

        // Update Conclusion
        $appraisal->is_approved = $request->is_approved;
        $appraisal->remarks = $request->remarks;
        $appraisal->update();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'appraisal' => $appraisal->with([
                'user.profile',
                'user.workPatterns.workTimings',
                'user.department.departmentHead.profile',
                'appraisalPolicies.questions.options',
                'appraisalPolicies.questions.appraisalAnswers' => function ($q) use ($request) {
                    $q->where('appraisal', $request->appraisal);
                },
                'practice.practiceManager.profile',
            ])
                ->latest('updated_at')
                ->first(),
        ]);
    }

    // Fetch overdue appraisals
    public function fetchOverdueAppraisals($request)
    {
        // Query builder for appraisals
        $overdueAppraisalsQuery = Appraisal::query();

        // if $request has practice
        if ($request->has('practice')) {
            $overdueAppraisalsQuery = $overdueAppraisalsQuery->where('practice', $request->practice);
        }

        // Fetch overdue appraisals
        $overdueAppraisals = $overdueAppraisalsQuery->where('date', '<', Carbon::now())
            ->where('is_completed', 0)
            ->orWhere('is_completed', null)
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'overdue-appraisals' => $overdueAppraisals,
        ]);
    }

    /* ---------------------------------- Appraisal Reschedule ---------------------------------- */

    // Create appraisal reschedule
    public function createAppraisalReschedule($request)
    {
        // Get appraisal
        $appraisal = Appraisal::findOrFail($request->appraisal);

        // Instance of AppraisalReschedule
        $appraisalReschedule = new AppraisalReschedule();
        $appraisalReschedule->appraisal = $appraisal->id;
        $appraisalReschedule->reason = $request->reason;
        $appraisalReschedule->date = $request->date;
        $appraisalReschedule->time = $request->time;
        $appraisalReschedule->duration = $request->duration;
        $appraisalReschedule->location = $request->location;
        $appraisalReschedule->save();

        // Change the status of the parent appraisal
        $appraisal->is_rescheduled = Config::get('constants.APPRAISAL.RESCHEDULED');
        $appraisal->update();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'appraisal-reschedule' => $appraisalReschedule,
        ]);
    }

    // Fetch appraisal reschedules
    public function fetchAppraisalReschedules($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Build query for appraisal reschedules
        $appraisalReschedulesQuery = AppraisalReschedule::query();

        // Filtered reschedules
        $filteredReschedules = $appraisalReschedulesQuery->whereHas('appraisal', function ($q) use ($practice) {
            $q->where('practice', $practice->id);
        })->latest()->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'appraisal-reschedules' => $filteredReschedules,
        ]);
    }
}