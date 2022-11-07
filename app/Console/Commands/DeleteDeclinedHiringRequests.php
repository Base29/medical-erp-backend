<?php

namespace App\Console\Commands;

use App\Models\HiringRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DeleteDeclinedHiringRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hiringRequest:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete declined hiring requests after 30 days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {

            // Get declined hiring requests
            $declinedHiringRequests = HiringRequest::where('status', 'declined')->get();

            // Loop through $declinedHiringRequests
            foreach ($declinedHiringRequests as $declinedHiringRequest):

                // Date when the hiring request is updated
                $updatedAt = new Carbon($declinedHiringRequest->updated_at);

                // Calculating the days past from the date of creation
                $daysPast = $updatedAt->diffInDays(Carbon::now());

                // Check if 30 days are passed after last updated_at
                if ($daysPast >= 30) {

                    // Delete declined hiring request older than 30 days
                    $declinedHiringRequest->delete();
                }
            endforeach;

        } catch (\Exception$e) {
            error_log($e);
        }
    }
}