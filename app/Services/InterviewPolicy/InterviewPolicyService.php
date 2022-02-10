<?php
namespace App\Services\InterviewPolicy;

use App\Helpers\Response;
use App\Models\InterviewPolicy;
use App\Models\InterviewQuestion;
use App\Models\InterviewQuestionOption;
use App\Models\Practice;
use Spatie\Permission\Models\Role;

class InterviewPolicyService
{
    // Create interview policy
    public function createInterviewPolicy($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get role
        $role = Role::findOrFail($request->role);

        // Instance of InterviewPolicy
        $interviewPolicy = new InterviewPolicy();
        $interviewPolicy->role_id = $role->id;
        $interviewPolicy->name = $request->name;
        $practice->interviewPolicies()->save($interviewPolicy);

        // Save questions for $interviewPolicy
        $this->saveQuestions($request->questions, $interviewPolicy);

        // Return success response
        return Response::success([
            'interview-policy' => $interviewPolicy->with('interviewQuestions.options', 'role', 'practice')
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
}