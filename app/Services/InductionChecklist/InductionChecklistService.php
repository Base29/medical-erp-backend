<?php
namespace App\Services\InductionChecklist;

use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\InductionChecklist;
use App\Models\InductionQuestion;
use App\Models\Practice;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role as ModelsRole;

class InductionChecklistService
{
    // Create induction checklist
    public function createInductionChecklist($request)
    {
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
        return $inductionChecklist->with('practice', 'role', 'inductionQuestions')
            ->latest()
            ->first();
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

    // Fetch induction checklists
    public function fetchInductionChecklists($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get induction checklists for a practice
        return InductionChecklist::where(['practice_id' => $practice->id, 'role_id' => $request->role])
            ->with('practice', 'role', 'inductionQuestions')
            ->latest()
            ->paginate(10);
    }

    // Fetch single induction checklist
    public function fetchSingleInductionChecklist($request)
    {
        // Get induction checklist
        $inductionChecklist = InductionChecklist::findOrFail($request->induction_checklist);

        // Return success response
        return $inductionChecklist->with('practice', 'role', 'inductionQuestions')
            ->where('id', $request->induction_checklist)
            ->first();
    }

    // Delete induction checklist
    public function deleteInductionChecklist($request)
    {
        // Get induction checklist
        $inductionChecklist = InductionChecklist::findOrFail($request->induction_checklist);

        // Delete induction checklist
        $inductionChecklist->delete();
    }

    // Update Induction checklist
    public function updateInductionChecklist($request)
    {
        // Allowed fields
        $allowedFields = [
            'name',
            'questions',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get induction checklist
        $inductionChecklist = InductionChecklist::findOrFail($request->induction_checklist);

        // Check if there is $request->questions array
        if ($request->has('questions')) {
            $this->updateQuestions($request->questions);
        }

        // Update induction checklist
        $inductionChecklistUpdated = UpdateService::updateModel($inductionChecklist, $request->all(), 'induction_checklist');

        // Return response if update fails
        if (!$inductionChecklistUpdated) {
            throw new \Exception(ResponseMessage::customMessage('Something went wrong cannot update Induction Checklist'));
        }

        // Return success response
        return $inductionChecklist->with('practice', 'role', 'inductionQuestions')
            ->latest('updated_at')
            ->first();
    }

    private function updateQuestions($questions)
    {

        // Loop through the $subItems array
        foreach ($questions as $question) {

            // Allowed fields
            $allowedFields = [
                'question',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!Arr::hasAny($question, $allowedFields)) {
                throw new \Exception(ResponseMessage::allowedFields($allowedFields));
            }

            // Get model depending on provided $tag gmc or nmc
            $model = InductionQuestion::findOrFail($question['id']);

            // Update subitem
            UpdateService::updateModel($model, $question, 'id');

        }

    }
}