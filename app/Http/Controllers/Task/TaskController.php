<?php

namespace App\Http\Controllers\Task;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\CheckList;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Method for creating a task
    public function create(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required',
            'frequency' => 'required',
            'checklist' => 'required|numeric',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if the checklist exists
        $checklist = CheckList::where('id', $request->checklist)->with('tasks')->first();

        if (!$checklist) {
            return response([
                'success' => false,
                'message' => 'Checklist not found with the provided id ' . $request->checklist,
            ], 404);
        }

        // Check if the task with same name already exists in the checklist
        $task_already_exist = $checklist->tasks->contains('name', $request->name);

        if ($task_already_exist) {
            return response([
                'success' => false,
                'message' => 'Task with name ' . $request->name . ' already exists in checklist ' . $checklist->name,
            ], 409);
        }

        // Create task
        $task = new Task();
        $task->name = $request->name;
        $task->check_list_id = $checklist->id;
        $task->frequency = $request->frequency;
        $task->save();

        return response([
            'success' => true,
            'task' => $task,
        ], 200);
    }

    // Method for deleting a task
    public function delete($id)
    {
        // Check if a task exists with a provided $id
        $task = Task::find($id);

        if (!$task) {
            return response([
                'success' => false,
                'message' => 'Task not found with the provided id ' . $id,
            ], 404);
        }

        // Delete task
        $task->delete();

        return response([
            'success' => true,
            'message' => 'Task deleted successfully',
        ], 200);
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
            return response([
                'success' => false,
                'message' => 'Update request should contain any of the allowed fields ' . implode("|", $allowed_fields),
            ], 400);
        }

        // Validation rules
        $rules = [
            'status' => 'boolean',
            'reason' => 'string',
            'comment' => 'alpha_dash', //TODO: Remove 'alpha_dash' validation and change it to string
            'manager_comment' => 'string',
            'acknowledgement' => 'boolean',
            'task' => 'required|numeric',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if the task exists
        $task = Task::find($request->task);

        if (!$task) {
            return response([
                'success' => false,
                'message' => 'Task with ID ' . $request->task . ' not found',
            ], 404);
        }

        // Update task's fields with the ones provided in the $request
        $task_updated = $this->update_task($request->all(), $task);

        if ($task_updated) {
            return response([
                'success' => true,
                'task' => $task,
            ]);
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