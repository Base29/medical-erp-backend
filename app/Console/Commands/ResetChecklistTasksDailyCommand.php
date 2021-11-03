<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;

class ResetChecklistTasksDailyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checklist:resetDaily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset daily tasks for checklists';

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

        // Get All Daily Tasks
        $tasks = Task::where('frequency', 'Daily')->with('checkList.room')->get();

        // Iterating through tasks
        foreach ($tasks as $task) {

            // Checking which task are active
            if ($task->is_active === 1) {

                // Getting the ID of the room to which the $task belongs to
                $roomId = $task->checkList->room->id;

                // Resetting the status of the room to 0 (false)
                $task->checkList->room->updateRoomStatus($roomId);

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