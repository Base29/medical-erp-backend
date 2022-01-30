<?php
namespace App\Services\Task;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\CheckList;
use App\Models\Task;
use Illuminate\Support\Carbon;

class TaskService
{
    // Create task
    public function createTask($request)
    {
        // Check if the checklist exists
        $checklist = CheckList::where('id', $request->checklist)->with('tasks')->firstOrFail();

        // Check if the task with same name already exists in the checklist
        $taskAlreadyExist = $checklist->tasks->contains('name', $request->name);

        if ($taskAlreadyExist) {
            throw new \Exception(ResponseMessage::alreadyExists($request->name));
        }

        // Create task
        $task = new Task();
        $task->name = $request->name;
        $task->check_list_id = $checklist->id;
        $task->frequency = $request->frequency;
        $task->save();

        return Response::success(['task' => $task]);
    }

    // Delete task
    public function deleteTask($id)
    {
        // Check if a task exists with a provided $id
        $task = Task::findOrFail($id);

        if (!$task) {
            throw new \Exception(ResponseMessage::notFound('Task', $id, false));
        }

        // Delete task
        $task->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Task')]);
    }

    // Update task
    public function updateTask($request)
    {
        // Allowed fields when updating a task
        $allowedFields = [
            'status',
            'reason',
            'comment',
            'manager_comment',
            'acknowledgement',
            'is_processed',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get Task
        $task = Task::findOrFail($request->task);

        // Get the time of update of the task
        $updatedAt = new Carbon($task->updated_at);

        // Get task frequency
        $taskFrequency = $task->frequency;

        // For monthly tasks $daysPast should be less than $daysForMonthlyTask
        $daysForMonthlyTask = 30;

        // For weekly tasks $daysPast should be less than $daysForWeeklyTask
        $daysForWeeklyTask = 7;

        // Get is_processed
        $isTaskProcessed = $task->is_processed;

        // If the task is not daily
        if ($isTaskProcessed === 1 && ($taskFrequency === 'Monthly' || $taskFrequency === 'Weekly')) {
            // Calculating the days past from the date of creation
            $daysPast = $updatedAt->diffInDays(Carbon::now());

            // Calculating days remaining
            $daysRemaining = Carbon::now()
                ->subDays($taskFrequency === 'Weekly' ? $daysForWeeklyTask : $daysForMonthlyTask)
                ->diffInDays($updatedAt);

            if ($daysPast < $daysForMonthlyTask || $daysPast < $daysForWeeklyTask) {
                return Response::fail([
                    'code' => 400,
                    'message' => ResponseMessage::customMessage('Task cannot be updated at the moment'),
                ]);
            }

        }

        // Update task's fields with the ones provided in the $request
        UpdateService::updateModel($task, $request->all(), 'task');

        // Return success response
        return Response::success(['task' => $task->latest('updated_at')->first()]);

    }
}