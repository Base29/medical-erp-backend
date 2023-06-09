<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('checklist:resetDaily')->dailyAt('03:00')->timezone('Europe/London');
        $schedule->command('checklist:resetWeekly')->dailyAt('03:00')->timezone('Europe/London');
        $schedule->command('checklist:resetMonthly')->dailyAt('03:00')->timezone('Europe/London');
        $schedule->command('hiringRequest:delete')->dailyAt('03:00')->timezone('Europe/London');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}