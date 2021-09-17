<?php

namespace App\Http\Controllers\Reason;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\Reason;
use Illuminate\Http\Request;

class ReasonController extends Controller
{

    // Create Reason
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
            ], 409);
        }

        // Create reason
        $reason = new Reason();
        $reason->reason = $request->reason;
        $reason->save();

        return response([
            'success' => true,
            'reason' => $reason,
        ], 200);
    }

    // Fetch Reasons
    public function fetch()
    {
        // Reasons
        $reasons = Reason::paginate(10);

        return response([
            'success' => true,
            'reasons' => $reasons,
        ], 200);
    }

    // Delete Reasons
    public function delete($id)
    {
        // Check if the reason exists with the provided ID
        $reason = Reason::find($id);

        if (!$reason) {
            return response([
                'success' => false,
                'message' => 'Reason with ID ' . $id . ' not found',
            ], 404);
        }

        // Delete reason
        $reason->delete();

        return response([
            'success' => true,
            'message' => 'Reason deleted',
        ], 200);
    }
}