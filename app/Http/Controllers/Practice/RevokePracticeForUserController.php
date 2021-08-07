<?php

namespace App\Http\Controllers\Practice;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RevokePracticeForUserController extends Controller
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