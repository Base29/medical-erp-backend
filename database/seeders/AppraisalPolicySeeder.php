<?php

namespace Database\Seeders;

use App\Models\AppraisalPolicy;
use App\Models\AppraisalQuestion;
use App\Models\AppraisalQuestionOption;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AppraisalPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Policy
        $policy = [
            'name' => 'Appraisal Policy',
        ];

        $questions = [
            [
                'question' => 'Question 1',
                'type' => 'multi-choice',
                'head' => 1,
                'options' => [
                    [
                        'option' => 'Option 1 for Question 1',
                    ],
                    [
                        'option' => 'Option 2 for Question 1',
                    ],
                ],
            ],
            [
                'question' => 'Question 2',
                'type' => 'descriptive',
                'head' => 2,
            ],
            [
                'question' => 'Question 3',
                'type' => 'single-choice',
                'head' => 3,
                'options' => [
                    [
                        'option' => 'Option 1 for Question 3',
                    ],
                    [
                        'option' => 'Option 2 for Question 3',
                    ],
                ],
            ],
            [
                'question' => 'Question 4',
                'type' => 'multi-choice',
                'head' => 3,
                'options' => [
                    [
                        'option' => 'Option 1 for Question 4',
                    ],
                    [
                        'option' => 'Option 2 for Question 4',
                    ],
                    [
                        'option' => 'Option 3 for Question 4',
                    ],
                    [
                        'option' => 'Option 4 for Question 4',
                    ],
                ],
            ],
        ];

        // Get roles
        $roles = Role::get();

        foreach ($roles as $role):
            if ($role['name'] !== 'super_admin' && $role['name'] !== 'admin' && $role['name'] !== 'hq' && $role['name'] !== 'manager' && $role['name'] !== 'recruiter') {
                // Instance of AppraisalPolicy
                $appraisalPolicy = new AppraisalPolicy();
                $appraisalPolicy->role = $role->id;
                $appraisalPolicy->name = $policy['name'] . ' - ' . $role['name'];
                $appraisalPolicy->save();

                // Save questions for $interviewPolicy
                $this->saveQuestions($questions, $appraisalPolicy->id);
            }
        endforeach;
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
}