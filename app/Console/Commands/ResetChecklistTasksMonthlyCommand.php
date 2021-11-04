<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ResetChecklistTasksMonthlyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checklist:resetMonthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset monthly tasks for checklists';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get All Weekly Tasks
        $tasks = Task::where('frequency', 'Monthly')->get();

        // Iterating through tasks
        foreach ($tasks as $task) {

            // Checking which task are active
            if ($task->is_active === 1) {

                // Getting the ID of the room to which the $task belongs to
                $roomId = $task->checkList->room->id;

                // Resetting the status of the room to 0 (false)
                $task->checkList->room->updateRoomStatus($roomId);

                // Date when the task is created
                $createdAt = new Carbon($task->created_at);

                // Calculating the days past from the date of creation
                $daysPast = $createdAt->diffInDays(Carbon::now());

                // If a week (7 days) has passed after the date of creation then reset the task
                if ($daysPast >= 30) {

                    // Replicating active tasks
                    $new_task = $task->replicate();

                    // Creating tasks
                    $new_task->status = 0;
                    $new_task->comment = null;
                    $new_task->reason = null;
                    $new_task->acknowledgement = 0;
                    $new_task->manager_comment = '';
                    $new_task->is_active = 1;
                    $new_task->save();

                    // Making old tasks in active
                    if ($task->id !== $new_task->id) {
                        $task->is_active = 0;
                        $task->save();
                    }

                }
            }

        }
    }
}