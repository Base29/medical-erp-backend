<?php

namespace App\Http\Controllers\InductionChecklist;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\InductionChecklist\CreateInductionChecklistRequest;
use App\Models\InductionChecklist;
use App\Models\InductionQuestion;
use App\Models\Practice;
use Spatie\Permission\Models\Role as ModelsRole;

class InductionChecklistController extends Controller
{
    // Create induction Checklist
    public function create(CreateInductionChecklistRequest $request)
    {
        try {
            // Get Practice
            $practice = Practice::findOrFail($request->practice);

            // Get Role
            $role = ModelsRole::findOrFail($request->role);

            // Instance of InductionChecklist model
            $inductionChecklist = new InductionChecklist();
            $inductionChecklist->name = $request->name;
            $inductionChecklist->role_id = $role->id;
            $practice->inductionChecklists()->save($inductionChecklist);

            $this->saveQuestions($inductionChecklist, $request->questions);

            // Return success response
            return Response::success([
                'induction-checklist' => $inductionChecklist->with('practice', 'inductionQuestions')->first(),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Save questions for induction checklist
    private function saveQuestions($inductionChecklist, $questions)
    {
        // Loop through the $questions array
        foreach ($questions as $question) {
            // Instance of InductionQuestion
            $inductionQuestion = new InductionQuestion();
            $inductionQuestion->question = $question['question'];
            $inductionChecklist->inductionQuestions()->save($inductionQuestion);
        }
    }
}