<?php
namespace App\Services\UserObjective;

use App\Helpers\Response;
use App\Models\Appraisal;
use App\Models\UserObjective;
use Illuminate\Support\Facades\Config;

class UserObjectiveService
{
    public function createUserObjective($request)
    {
        // Get appraisal
        $appraisal = Appraisal::findOrFail($request->appraisal);

        // Get user from appraisal
        $user = $appraisal->user;

        // Initiate empty array
        $savedUserObjectives = [];

        // Cast $request->objectives to variable
        $objectives = $request->objectives;

        // Loop through $objectives
        foreach ($objectives as $objective):
            // Create user objective
            $userObjective = new UserObjective();
            $userObjective->user = $user;
            $userObjective->objective = $objective['objective'];
            $userObjective->due_date = $objective['due_date'];
            $userObjective->status = Config::get('constants.USER_OBJECTIVE.INCOMPLETE');

            // Save $userObjective
            $userObjective->save();

            array_push($savedUserObjectives, $userObjective);
        endforeach;

        // Convert $savedUserObjectives to collection
        $objectivesCollection = collect($savedUserObjectives);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_CREATED,
            'user-objectives' => $objectivesCollection,
        ]);

    }
}