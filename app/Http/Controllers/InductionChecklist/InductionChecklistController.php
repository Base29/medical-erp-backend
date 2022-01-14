<?php

namespace App\Http\Controllers\InductionChecklist;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\InductionChecklist\CreateInductionChecklistRequest;
use App\Http\Requests\InductionChecklist\DeleteInductionChecklistRequest;
use App\Http\Requests\InductionChecklist\FetchInductionChecklistRequest;
use App\Http\Requests\InductionChecklist\FetchSingleInductionChecklistRequest;
use App\Http\Requests\InductionChecklist\UpdateInductionChecklistRequest;
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
                'induction-checklist' => $inductionChecklist->with('practice', 'role', 'inductionQuestions')
                    ->where('id', $request->induction_checklist)
                    ->first(),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete induction checklist
    public function delete(DeleteInductionChecklistRequest $request)
    {
        try {
            // Get induction checklist
            $inductionChecklist = InductionChecklist::findOrFail($request->induction_checklist);

            // Delete induction checklist
            $inductionChecklist->delete();

            // Return success response
            return Response::success([
                'message' => ResponseMessage::deleteSuccess('Induction Checklist'),
            ]);
        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update induction checklist
    public function update(UpdateInductionChecklistRequest $request)
    {
        try {
            // Allowed fields
            $allowedFields = [
                'name',
                'questions',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!$request->hasAny($allowedFields)) {
                return Response::fail([
                    'message' => ResponseMessage::allowedFields($allowedFields),
                    'code' => 400,
                ]);
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
                return Response::fail([
                    'code' => 400,
                    'message' => ResponseMessage::customMessage('Something went wrong cannot update Induction Checklist'),
                ]);
            }

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function updateQuestions($questions)
    {
        try {
            // Loop through the $subItems array
            foreach ($questions as $question) {

                // Allowed fields
                $allowedFields = [
                    'question',
                ];

                // Checking if the $request doesn't contain any of the allowed fields
                if (!$question->hasAny($allowedFields)) {
                    return Response::fail([
                        'message' => ResponseMessage::allowedFields($allowedFields),
                        'code' => 400,
                    ]);
                }

                // Get model depending on provided $tag gmc or nmc
                $model = InductionQuestion::findOrFail($question['id']);

                // Update subitem
                UpdateService::updateModel($model, $question, 'id');

            }
        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}