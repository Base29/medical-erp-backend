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

        try {

            // Create practice with the provided name
            $practice = new Practice();
            $practice->practice_name = $request->name;
            $practice->save();

            return Response::success(['practice' => $practice]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }

    }

    // Method for deleting practice
    public function delete($id)
    {
        try {

            // Check if practice exists
            $practice = Practice::findOrFail($id);

            if (!$practice) {
                return Response::fail([
                    'message' => ResponseMessage::notFound('Practice', $id, false),
                    'code' => 404,
                ]);
            }

            // Deleting practice
            $practice->delete();

            return Response::success(['message' => ResponseMessage::deleteSuccess('Practice')]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }

    }

    // Method for fetching practices
    public function fetch()
    {
        try {

            // Fetch practices
            $practices = Practice::with('policies')->latest()->paginate(10);

            return Response::success(['practices' => $practices]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for assigning user to practice
    public function assignToUser(AssignPracticeToUserRequest $request)
    {

        try {

            // Get User
            $user = User::where('email', $request->email)->firstOrFail();

            // Get Practice
            $practice = Practice::findOrFail($request->practice);

            // Checking if the user is already assigned to the provided practice
            $userAlreadyAssignedToPractice = $user->practices->contains('id', $practice->id);

            if ($userAlreadyAssignedToPractice) {
                return Response::fail([
                    'message' => ResponseMessage::alreadyAssigned($user->email, $practice->practice_name),
                    'code' => 409,
                ]);
            }

            // Attach user to practice
            $user->practices()->attach($practice->id);

            return Response::success(['message' => ResponseMessage::assigned($user->email, $practice->practice_name)]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }

    }

    // Method for revoking user from practice
    public function revokeForUser(RevokePracticeForUserRequest $request)
    {

        try {

            // Get User
            $user = User::where('email', $request->email)->firstOrFail();

            // Get Practice
            $practice = Practice::findOrFail($request->practice);

            // Check if the user is already assigned to the practice
            $associatedToPractice = $user->practices->contains('id', $practice->id);

            if (!$associatedToPractice) {
                return Response::fail([
                    'message' => ResponseMessage::notBelongTo($user->email, $practice->practice_name),
                    'code' => 409,
                ]);
            }

            // Revoke user from practice
            $user->practices()->detach($practice->id);

            return Response::success(['message' => ResponseMessage::revoked($user->email, $practice->practice_name)]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}