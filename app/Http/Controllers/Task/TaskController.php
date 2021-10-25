<?php

namespace App\Http\Controllers\Task;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Models\CheckList;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Method for creating a task
    public function create(CreateTaskRequest $request)
    {
        // Check if the checklist exists
        $checklist = CheckList::where('id', $request->checklist)->with('tasks')->first();

        // Check if the task with same name already exists in the checklist
        $task_already_exist = $checklist->tasks->contains('name', $request->name);

        if ($task_already_exist) {
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
    }

    // Method for deleting a task
    public function delete($id)
    {
        // Check if a task exists with a provided $id
        $task = Task::find($id);

        if (!$task) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Task', $id, false),
                'code' => 404,
            ]);
        }

        // Delete task
        $task->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Task')]);
    }

    public function update(Request $request)
    {
        // Allowed fields when updating a task
        $allowed_fields = [
            'status',
            'reason',
            'comment',
            'manager_comment',
            'acknowledgement',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowed_fields)) {
            return Response::fail([
                'message' => ResponseMessage::allowedFields($allowed_fields),
                'code' => 400,
            ]);
        }

        // Get Task
        $task = Task::find($request->task);

        // Update task's fields with the ones provided in the $request
        $task_updated = $this->update_task($request->all(), $task);

        if ($task_updated) {
            return Response::success(['task' => $task]);
        }

    }

    private function update_task($fields, $task)
    {
        foreach ($fields as $field => $value) {
            if ($field !== 'task') {
                $task->$field = $value;
            }
        }
        $task->save();
        return true;
    }
}