<?php

namespace App\Http\Controllers\Reason;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\Reason;
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

        // Check if the reason exists
        $reason_exist = Reason::where('reason', $request->reason)->first();

        if ($reason_exist) {
            return response([
                'success' => false,
                'message' => 'Reason ' . $reason_exist->reason . ' already exists',
            ]);
        }

        // Create reason
        $reason = new Reason();
        $reason->reason = $request->reason;
        $reason->save();

        return response([
            'success' => true,
            'reason' => $reason,
        ]);
    }
}