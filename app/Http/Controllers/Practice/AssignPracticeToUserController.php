<?php

namespace App\Http\Controllers\Practice;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssignPracticeToUserController extends Controller
{
    public function __invoke(Request $request)
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
}