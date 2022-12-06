<?php
namespace App\Services\UserObjective;

use App\Helpers\Response;
use App\Helpers\UpdateService;
use App\Models\Appraisal;
use App\Models\User;
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

    // Update user objective
    public function updateUserObjective($request)
    {
        // Get user objective
        $userObjective = UserObjective::findOrFail($request->user_objective);

        // Update user objective
        UpdateService::updateModel($userObjective, $request->validated(), 'user_objective');

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'user-objective' => $userObjective->latest('updated_at')->first(),
        ]);
    }

    // Delete user objective
    public function deleteUserObjective($request)
    {
        // Get user objective
        $userObjective = UserObjective::findOrFail($request->user_objective);

        // Delete user objective
        $userObjective->delete();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'user-objective' => $userObjective,
        ]);
    }

    // Fetch user objectives
    public function fetchUserObjectives($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Build query for user objectives
        $userObjectivesQuery = UserObjective::query();

        // If $request has status
        if ($request->has('status')) {
            $userObjectivesQuery = $userObjectivesQuery->where('status', $request->status);
        }

        // Filtered user objectives
        $filteredUserObjectives = $userObjectivesQuery->where('user', $user->id)->latest()->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'user-objectives' => $filteredUserObjectives,
        ]);

    }
}