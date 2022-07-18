<?php
namespace App\Services\InductionResult;

use App\Helpers\ResponseMessage;
use App\Models\InductionChecklist;
use App\Models\InductionResult;
use App\Models\InductionSchedule;

class InductionResultService
{
    // Create induction result
    public function createInductionResult($request)
    {
        // Get induction schedule
        $inductionSchedule = InductionSchedule::findOrFail($request->induction_schedule);

        // Get induction checklist
        $inductionChecklist = InductionChecklist::findOrFail($request->induction_checklist);

        // Check if the provided $inductionChecklist is a part of  $inductionSchedule
        if (!$inductionSchedule->belongsToSchedule($inductionChecklist->id)) {
            throw new \Exception('Induction Checklist ' . $inductionChecklist->id . ' is not part of the induction schedule');
        }

        // Check if the user has already completed the induction of $inductionChecklist
        if ($inductionChecklist->resultAlreadyGenerated($inductionSchedule->user_id)) {
            throw new \Exception(ResponseMessage::customMessage('User ' . $inductionSchedule->user_id . ' already completed the induction for the checklist ' . $inductionChecklist->id));
        }

        // Initiate empty array for induction results
        $inductionResults = [];

        // Loop through $request->questions array
        foreach ($request->questions as $question) {

            // Instance of InductionResult model
            $inductionResult = new InductionResult();
            $inductionResult->user_id = $inductionSchedule->user_id;
            $inductionResult->induction_schedule_id = $inductionSchedule->id;
            $inductionResult->induction_question_id = $question['id'];
            $inductionResult->completed = $question['completed'];

            // Save result
            $inductionChecklist->inductionResults()->save($inductionResult);

            array_push($inductionResults, $inductionResult);

        }

        // Return induction results
        return $inductionResults;
    }

    // Duplicating the above method for testing
    public function createInductionResultSingle($request)
    {
        // Get induction schedule
        $inductionSchedule = InductionSchedule::findOrFail($request->induction_schedule);

        // Get induction checklist
        $inductionChecklist = InductionChecklist::findOrFail($request->induction_checklist);

        // Check if the provided $inductionChecklist is a part of  $inductionSchedule
        if (!$inductionSchedule->belongsToSchedule($inductionChecklist->id)) {
            throw new \Exception('Induction Checklist ' . $inductionChecklist->id . ' is not part of the induction schedule');
        }

        // Check if the user has already completed the induction of $inductionChecklist
        if ($inductionChecklist->resultAlreadyGenerated($inductionSchedule->user_id)) {
            throw new \Exception(ResponseMessage::customMessage('User ' . $inductionSchedule->user_id . ' already completed the induction for the checklist ' . $inductionChecklist->id));
        }

        // Instance of InductionResult model
        $inductionResult = new InductionResult();
        $inductionResult->user_id = $inductionSchedule->user_id;
        $inductionResult->induction_schedule_id = $inductionSchedule->id;
        $inductionResult->induction_question_id = $request->question;
        $inductionResult->comment = $request->comment;
        $inductionResult->completed = $request->completed;

        // Save result
        $inductionChecklist->inductionResults()->save($inductionResult);

        // Return induction results
        return $inductionResult;
    }
}