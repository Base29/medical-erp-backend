<?php

namespace App\Http\Controllers\Practice;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\AssignPracticeToUserRequest;
use App\Http\Requests\Practice\CreatePracticeRequest;
use App\Http\Requests\Practice\RevokePracticeForUserRequest;
use App\Models\Practice;
use App\Models\User;

class PracticeController extends Controller
{
    // Method for creating practices
    public function create(CreatePracticeRequest $request)
    {

        // Create practice with the provided name
        $practice = Practice::create([
            'practice_name' => $request->name,
        ]);

        return Response::success(['practice' => $practice]);
    }

    // Method for deleting practice
    public function delete($id)
    {
        // Check if practice exists
        $practice = Practice::find($id);

        if (!$practice) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Practice', $id, false),
                'code' => 404,
            ]);
        }

        // Deleting practice
        $practice->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Practice')]);

    }

    // Method for fetching practices
    public function fetch()
    {
        // Fetch practices
        $practices = Practice::with('policies')->paginate(10);

        return Response::success(['practices' => $practices]);
    }

    // Method for assigning user to practice
    public function assign_to_user(AssignPracticeToUserRequest $request)
    {

        // Get User
        $user = User::where('email', $request->email)->first();

        // Get Practice
        $practice = Practice::find($request->practice);

        // Checking if the user is already assigned to the provided practice
        $user_already_assigned_to_practice = $user->practices->contains('id', $practice->id);

        if ($user_already_assigned_to_practice) {
            return Response::fail([
                'message' => ResponseMessage::alreadyAssigned($user->email, $practice->practice_name),
                'code' => 409,
            ]);
        }

        // Attach user to practice
        $user->practices()->attach($practice->id);

        return Response::success(['message' => ResponseMessage::assigned($user->email, $practice->practice_name)]);

    }

    // Method for revoking user from practice
    public function revoke_for_user(RevokePracticeForUserRequest $request)
    {

        // Get User
        $user = User::where('email', $request->email)->first();

        // Get Practice
        $practice = Practice::find($request->practice);

        // Check if the user is already assigned to the practice
        $associated_to_practice = $user->practices->contains('id', $practice->id);

        if (!$associated_to_practice) {
            return Response::fail([
                'message' => ResponseMessage::notBelongTo($user->email, $practice->practice_name),
                'code' => 409,
            ]);
        }

        // Revoke user from practice
        $user->practices()->detach($practice->id);

        return Response::success(['message' => ResponseMessage::revoked($user->email, $practice->practice_name)]);
    }
}