<?php

namespace App\Http\Controllers\Practice;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PracticeController extends Controller
{
    // Method for creating practices
    public function create(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            // Return error messages against $rules
            return CustomValidation::error_messages($rules, $validator);
        }

        // Check if the practice already exist
        $practice_exists = Practice::where('practice_name', $request->name)->first();

        if ($practice_exists) {
            return response([
                'success' => false,
                'message' => 'Practice with name ' . $request->name . ' already exists',
            ], 409);
        }

        // Create practice with the provided name
        $practice = Practice::create([
            'practice_name' => $request->name,
        ]);

        return response([
            'success' => true,
            'message' => 'Practice with name ' . $practice->practice_name . ' created successfully',
        ], 200);
    }

    // Method for deleting practice
    public function delete($id)
    {
        // Check if practice exists
        $practice = Practice::find($id);

        if (!$practice) {
            return response([
                'success' => false,
                'message' => 'Practice with the provided id ' . $id . ' doesn\'t exists',
            ], 404);
        }

        // Deleting practice
        $practice->delete();

        return response([
            'success' => true,
            'message' => 'Practice deleted successfully',
        ], 200);

    }

    // Method for fetching practices
    public function fetch()
    {
        // Fetch practices
        $practices = Practice::with('policies')->paginate(10);

        return response([
            'success' => true,
            'practices' => $practices,
        ], 200);
    }

    // Method for assigning user to practice
    public function assign_to_user(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email',
            'practice' => 'required|numeric',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            // Return error messages against $rules
            return CustomValidation::error_messages($rules, $validator);
        }

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response([
                'success' => false,
                'message' => 'User with email ' . $request->email . ' doesn\'t exists',
            ], 404);
        }

        // Check if the practice exist that is being assigned to the user
        $practice = Practice::find($request->practice);

        if (!$practice) {
            return response([
                'success' => false,
                'message' => 'Practice with id ' . $request->practice . ' doesn\'t exists',
            ], 404);
        }

        // Checking if the user is already assigned to the provided practice
        $user_already_assigned_to_practice = $user->practices->contains('id', $request->practice);

        if ($user_already_assigned_to_practice) {
            return response([
                'success' => true,
                'message' => 'User ' . $user->email . ' already assigned to practice ' . $practice->practice_name,
            ], 409);
        }

        // Attach user to practice
        $user->practices()->attach($practice->id);

        return response([
            'success' => true,
            'message' => 'User ' . $user->email . ' assigned to practice ' . $practice->practice_name,
        ], 200);

    }

    // Method for revoking user from practice
    public function revoke_for_user(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email',
            'practice' => 'required|numeric',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            // Return error messages against $rules
            return CustomValidation::error_messages($rules, $validator);
        }

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response([
                'success' => false,
                'message' => 'User ' . $request->email . ' doesn\'t exists',
            ], 404);
        }

        // Check if the practice exists with the provided id
        $practice = Practice::find($request->practice);

        if (!$practice) {
            return response([
                'success' => false,
                'message' => 'Practice not found',
            ], 404);
        }

        // Check if the user is already assigned to the practice
        $associated_to_practice = $user->practices->contains('id', $request->practice);

        if (!$associated_to_practice) {
            return response([
                'success' => false,
                'message' => 'User ' . $user->email . ' is not associated with practice ' . $practice->practice_name,
            ], 409);
        }

        // Revoke user from practice
        $user->practices()->detach($practice->id);

        return response([
            'success' => true,
            'message' => 'User ' . $user->email . ' removed from practice ' . $practice->practice_name,
        ], 200);
    }
}