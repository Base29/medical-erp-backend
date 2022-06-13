<?php
namespace App\Services\InterviewPolicy;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\InterviewPolicy;
use App\Models\InterviewQuestion;
use App\Models\InterviewQuestionOption;
use App\Models\Practice;
use App\Models\Role;

class InterviewPolicyService
{
    // Create interview policy
    public function createInterviewPolicy($request)
    {
        // Get practice
        // $practice = Practice::findOrFail($request->practice);

        // Get role
        $role = Role::findOrFail($request->role);

        if ($role->hasInterviewPolicy()) {
            throw new \Exception(ResponseMessage::customMessage('Role ' . $role->name . ' already have a interview policy'));
        }

        // Instance of InterviewPolicy
        $interviewPolicy = new InterviewPolicy();
        $interviewPolicy->role_id = $role->id;
        $interviewPolicy->name = $request->name;
        $interviewPolicy->save();

        // Save questions for $interviewPolicy
        $this->saveQuestions($request->questions, $interviewPolicy);

        // Return success response
        return Response::success([
            'interview-policy' => $interviewPolicy->with('interviewQuestions.options', 'role', 'practice')
                ->latest()
                ->first(),
        ]);

    }

    // Save questions
    public function saveQuestions($questions, $interviewPolicy)
    {
        // Iterate through $questions array
        foreach ($questions as $question) {
            // Instance of InterviewQuestion
            $interviewQuestion = new InterviewQuestion();
            $interviewQuestion->type = $question['type'];
            $interviewQuestion->head = $question['head'];
            $interviewQuestion->question = $question['question'];

            // Save question
            $interviewPolicy->interviewQuestions()->save($interviewQuestion);

            // Check if $interviewQuestion is multi-choice or single-choice
            if ($interviewQuestion->type === 'multi-choice' || $interviewQuestion->type === 'single-choice') {
                // Save options for $interviewQuestion
                $this->saveOptions($question['options'], $interviewQuestion);
            }

        }
    }

    // Save options for questions
    public function saveOptions($options, $interviewQuestion)
    {
        // Iterate through $options array
        foreach ($options as $option) {
            // Instance of InterviewQuestionOption
            $interviewQuestionOption = new InterviewQuestionOption();
            $interviewQuestionOption->option = $option['option'];

            // Save InterviewQuestionOption
            $interviewQuestion->options()->save($interviewQuestionOption);
        }
    }

    // Fetch all interview policies belongs to a practice
    public function fetchAllInterviewPolicies()
    {

        // Get all interview policies
        $interviewPolicies = InterviewPolicy::with('interviewQuestions.options', 'practice', 'role')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'interview-policies' => $interviewPolicies,
        ]);

    }

    // Fetch interview policies for a practice
    public function fetchPracticeInterviewPolicies($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get all interview policies of $practice
        $interviewPolicies = InterviewPolicy::where('practice_id', $practice->id)
            ->with('interviewQuestions.options', 'practice', 'role')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'interview-policies' => $interviewPolicies,
        ]);
    }

    // Fetch single interview policy
    public function fetchSingleInterviewPolicy($request)
    {
        // Get interview policy
        $interviewPolicy = InterviewPolicy::where('id', $request->interview_policy)
            ->with('interviewQuestions.options', 'practice', 'role')
            ->firstOrFail();

        // Return success response
        return Response::success([
            'interview-policy' => $interviewPolicy,
        ]);
    }

    // Delete interview policy
    public function deleteInterviewPolicy($request)
    {
        // Get interview policy
        $interviewPolicy = InterviewPolicy::findOrFail($request->interview_policy);

        // Delete interview policy
        $interviewPolicy->delete();

        // Return success response
        return Response::success([
            'message' => ResponseMessage::deleteSuccess('Interview Policy'),
        ]);
    }
}