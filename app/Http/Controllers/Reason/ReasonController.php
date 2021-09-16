<?php

namespace App\Http\Controllers\Reason;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReasonController extends Controller
{
    public function create(Request $request)
    {
        // Validation rules
        $rules = [
            'reason' => 'required',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        return $request->all();
    }
}