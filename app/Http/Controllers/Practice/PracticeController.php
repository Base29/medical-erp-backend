<?php

namespace App\Http\Controllers\Practice;

use App\Helpers\CustomValidation;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Http\Request;

class PracticeController extends Controller
{
    // Method for creating practices
    public function create(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if the practice already exist
        $practice_exists = Practice::where('practice_name', $request->name)->first();

        if ($practice_exists) {
            return Response::fail([
                'message' => ResponseMessage::alreadyExists($practice_exists->practice_name),
                'code' => 409,
            ]);
        }

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
    public function assign_to_user(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email',
            'practice' => 'required|numeric',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return Response::fail([
                'message' => ResponseMessage::notFound('User', $request->email, true),
                'code' => 404,
            ]);
        }

        // Check if the practice exist that is being assigned to the user
        $practice = Practice::find($request->practice);

        if (!$practice) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Practice', $request->practice, false),
                'code' => 404,
            ]);
        }

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
    public function revoke_for_user(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email',
            'practice' => 'required|numeric',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return Response::fail([
                'message' => ResponseMessage::notFound('User', $request->email, true),
                'code' => 404,
            ]);
        }

        // Check if the practice exists with the provided id
        $practice = Practice::find($request->practice);

        if (!$practice) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Practice', $request->practice, false),
                'code' => 404,
            ]);
        }

        // Check if the user is already assigned to the practice
        $associated_to_practice = $user->practices->contains('id', $request->practice);

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