<?php

namespace App\Http\Controllers\Practice;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\Practice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreatePracticeController extends Controller
{
    public function __invoke(Request $request)
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
}