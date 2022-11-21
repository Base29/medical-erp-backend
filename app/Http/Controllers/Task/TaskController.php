<?php

namespace App\Http\Controllers\Task;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Services\Task\TaskService;
use Exception;

class TaskController extends Controller
{

    // Local variable
    protected $taskService;

    // Constructor
    public function __construct(TaskService $taskService)
    {
        // Inject Service
        $this->taskService = $taskService;
    }

    // Method for creating a task
    public function create(CreateTaskRequest $request)
    {
        try {

            // Create task
            return $this->taskService->createTask($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for deleting a task
    public function delete($id)
    {

        try {

            // Delete task
            return $this->taskService->deleteTask($id);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function update(UpdateTaskRequest $request)
    {

        try {

            // Update task service
            return $this->taskService->updateTask($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}