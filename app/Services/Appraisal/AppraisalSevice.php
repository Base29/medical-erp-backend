<?php
namespace App\Services\Appraisal;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\Appraisal;
use App\Models\AppraisalPolicy;
use App\Models\AppraisalQuestion;
use App\Models\AppraisalQuestionOption;
use App\Models\Department;
use App\Models\Practice;
use App\Models\Role;
use App\Models\User;

class AppraisalService
{
    public function createAppraisal($request)
    {
        $practice = Practice::findOrFail($request->practice);

        // Get user
        $user = User::findOrFail($request->user);

        // Get Interview policy
        $appraisalPolicy = AppraisalPolicy::where('role', $user->roles[0]->id)->firstOrFail();

        if (!$appraisalPolicy) {
            throw new \Exception(ResponseMessage::customMessage('No appraisal policy associated with role ' . $user->roles[0]->id));
        }

        // Get department
        $department = Department::findOrFail($request->department);

        // Instance of InterviewSchedule
        $appraisal = new Appraisal();
        $appraisal->user = $user->id;
        $appraisal->department = $department->id;
        $appraisal->practice = $practice->id;
        $appraisal->date = $request->date;
        $appraisal->time = $request->time;
        $appraisal->location = $request->location;
        $appraisal->duration = $request->duration;
        $appraisal->type = $request->type;
        $appraisal->status = $request->status;
        $appraisal->additional_staff = $request->additional_staff;
        $appraisal->hq_staff = $request->hq_staff;

        // Save Appraisal schedule
        $appraisal->save();

        // Attach Appraisal Policy
        $appraisal->appraisalPolicies()->attach($appraisalPolicy->id);

        // Return success response
        return Response::success([
            'appraisal' => $appraisal->with('practice', 'appraisalPolicies.questions.options', 'department.departmentHead.profile', 'user.profile')
                ->latest()
                ->first(),
        ]);
    }

    // Create appraisal policy
    public function createAppraisalPolicy($request)
    {
        // Get practice
        // $practice = Practice::findOrFail($request->practice);

        // Get role
        $role = Role::findOrFail($request->role);

        if ($role->hasInterviewPolicy()) {
            throw new \Exception(ResponseMessage::customMessage('Role ' . $role->name . ' already have a interview policy'));
        }

        // Instance of AppraisalPolicy
        $appraisalPolicy = new AppraisalPolicy();
        $appraisalPolicy->role_id = $role->id;
        $appraisalPolicy->name = $request->name;
        $appraisalPolicy->save();

        // Save questions for $interviewPolicy
        $this->saveQuestions($request->questions, $appraisalPolicy);

        // Return success response
        return Response::success([
            'appraisal-policy' => $appraisalPolicy->with('questions.options', 'role', 'practice')
                ->latest()
                ->first(),
        ]);

    }

    // Save questions
    public function saveQuestions($questions, $appraisalPolicy)
    {
        // Iterate through $questions array
        foreach ($questions as $question) {
            // Instance of InterviewQuestion
            $appraisalQuestion = new AppraisalQuestion();
            $appraisalQuestion->type = $question['type'];
            $appraisalQuestion->head = $question['head'];
            $appraisalQuestion->question = $question['question'];

            // Save question
            $appraisalPolicy->questions()->save($appraisalQuestion);

            // Check if $interviewQuestion is multi-choice or single-choice
            if ($appraisalQuestion->type === 'multi-choice' || $appraisalQuestion->type === 'single-choice') {
                // Save options for $interviewQuestion
                $this->saveOptions($question['options'], $appraisalQuestion);
            }

        }
    }

    // Save options for questions
    public function saveOptions($options, $interviewQuestion)
    {
        // Iterate through $options array
        foreach ($options as $option) {
            // Instance of InterviewQuestionOption
            $appraisalQuestionOption = new AppraisalQuestionOption();
            $appraisalQuestionOption->option = $option['option'];

            // Save InterviewQuestionOption
            $interviewQuestion->options()->save($appraisalQuestionOption);
        }
    }

    // Fetch all interview policies belongs to a practice
    public function fetchAllAppraisalPolicies()
    {

        // Get all interview policies
        $appraisalPolicies = AppraisalPolicy::with('questions.options', 'practice', 'role')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'appraisal-policies' => $appraisalPolicies,
        ]);

    }

    // Fetch interview policies for a practice
    public function fetchPracticeAppraisalPolicies($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get all interview policies of $practice
        $appraisalPolicies = AppraisalPolicy::where('practice_id', $practice->id)
            ->with('questions.options', 'practice', 'role')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
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
            'appraisal-policy' => $appraisalPolicy,
        ]);
    }

    // Delete interview policy
    public function deleteInterviewPolicy($request)
    {
        // Get interview policy
        $appraisalPolicy = AppraisalPolicy::findOrFail($request->appraisal_policy);

        // Delete interview policy
        $appraisalPolicy->delete();

        // Return success response
        return Response::success([
            'message' => ResponseMessage::deleteSuccess('Appraisal Policy'),
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
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get interview policy
        $appraisalPolicy = AppraisalPolicy::findOrFail($request->appraisal_policy);

        // Update interview policy
        $appraisalPolicyUpdated = UpdateService::updateModel($appraisalPolicy, $request->validated(), 'appraisal_policy');

        // Return response if update fails
        if (!$appraisalPolicyUpdated) {
            throw new \Exception(ResponseMessage::customMessage('Something went wrong. Cannot update Appraisal Policy'));
        }

        // Return success response
        return Response::success([
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
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
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
            'interview-question' => $appraisalQuestion->with('options')->latest('updated_at')->first(),
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
}