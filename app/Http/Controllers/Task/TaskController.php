<?php

namespace App\Http\Controllers\Task;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
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

        // // Check if the checklist exists
        // $checklist = CheckList::where('id', $request->checklist)->with('tasks')->first();

        // if (!$checklist) {
        //     return response([
        //         'success' => false,
        //         'message' => 'Checklist not found with the provided id ' . $request->checklist,
        //     ], 404);
        // }

        // // Check if the task with same name already exists in the checklist
        // $task_already_exist = $checklist->tasks->contains('name', $request->name);

        // if ($task_already_exist) {
        //     return response([
        //         'success' => false,
        //         'message' => 'Task with name ' . $request->name . ' already exists in checklist ' . $checklist->name,
        //     ], 409);
        // }

        // // Create task
        // $task = new Task();
        // $task->name = $request->name;
        // $task->check_list_id = $checklist->id;
        // $task->frequency = $request->frequency;
        // $task->save();

        // return response([
        //     'success' => true,
        //     'message' => 'Task created successfully',
        // ], 200);
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

    public function update($id, Request $request)
    {
        $allowed_fields = [
            'status',
            'reason',
            'comment',
        ];
    }
}