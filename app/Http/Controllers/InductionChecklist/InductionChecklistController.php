<?php

namespace App\Http\Controllers\InductionChecklist;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\InductionChecklist\CreateInductionChecklistRequest;
use App\Http\Requests\InductionChecklist\FetchInductionChecklistRequest;
use App\Http\Requests\InductionChecklist\FetchSingleInductionChecklistRequest;
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
                'induction-checklist' => $inductionChecklist->with('practice', 'role', 'inductionQuestions')
                    ->latest()
                    ->first(),
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

    // Fetch induction checklists for a practice
    public function fetch(FetchInductionChecklistRequest $request)
    {
        try {

            // Get practice
            $practice = Practice::findOrFail($request->practice);

            // Get induction checklists for a practice
            $inductionChecklists = InductionChecklist::where('practice_id', $practice->id)
                ->with('practice', 'role', 'inductionQuestions')
                ->latest()
                ->paginate(10);

            // Return success response
            return Response::success([
                'induction-checklists' => $inductionChecklists,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single induction checklist
    public function fetchSingle(FetchSingleInductionChecklistRequest $request)
    {
        try {
            // Get induction checklist
            $inductionChecklist = InductionChecklist::findOrFail($request->induction_checklist);

            // Return success response
            return Response::success([
                'induction-checklist' => $inductionChecklist->with('practice', 'role', 'inductionQuestions'),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}