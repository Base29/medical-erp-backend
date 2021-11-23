<?php

namespace App\Http\Controllers\Task;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\CheckList;
use App\Models\Task;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    // Method for creating a task
    public function create(CreateTaskRequest $request)
    {
        try {

            // Check if the checklist exists
            $checklist = CheckList::where('id', $request->checklist)->with('tasks')->firstOrFail();

            // Check if the task with same name already exists in the checklist
            $taskAlreadyExist = $checklist->tasks->contains('name', $request->name);

            if ($taskAlreadyExist) {
                return Response::fail([
                    'message' => ResponseMessage::alreadyExists($request->name),
                    'code' => 409,
                ]);
            }

            // Create task
            $task = new Task();
            $task->name = $request->name;
            $task->check_list_id = $checklist->id;
            $task->frequency = $request->frequency;
            $task->save();

            return Response::success(['task' => $task]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for deleting a task
    public function delete($id)
    {

        try {

            // Check if a task exists with a provided $id
            $task = Task::findOrFail($id);

            if (!$task) {
                return Response::fail([
                    'message' => ResponseMessage::notFound('Task', $id, false),
                    'code' => 404,
                ]);
            }

            // Delete task
            $task->delete();

            return Response::success(['message' => ResponseMessage::deleteSuccess('Task')]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function update(UpdateTaskRequest $request)
    {

        try {

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
                return Response::fail([
                    'message' => ResponseMessage::allowedFields($allowedFields),
                    'code' => 400,
                ]);
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
            if ($taskFrequency === 'Monthly' || $taskFrequency === 'Weekly' && $isTaskProcessed === 1) {
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
            $taskUpdated = UpdateService::updateModel($task, $request->all(), 'task');

            if ($taskUpdated) {
                return Response::success(['task' => $task->latest('updated_at')->first()]);
            }

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}