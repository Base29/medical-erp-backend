<?php

namespace App\Http\Controllers\Reason;

use App\Helpers\CustomValidation;
use App\Helpers\Response;
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
            return Response::fail([
                'message' => 'Reason ' . $reason_exist->reason . ' already exists',
                'code' => 409,
            ]);
        }

        // Create reason
        $reason = new Reason();
        $reason->reason = $request->reason;
        $reason->save();

        return Response::success(['reason' => $reason]);
    }

    // Fetch Reasons
    public function fetch()
    {
        // Reasons
        $reasons = Reason::paginate(10);

        return Response(['reasons' => $reasons]);
    }

    // Delete Reasons
    public function delete($id)
    {
        // Check if the reason exists with the provided ID
        $reason = Reason::find($id);

        if (!$reason) {
            return Response::fail([
                'message' => 'Reason with ID ' . $id . ' not found',
                'code' => 404,
            ]);
        }

        // Delete reason
        $reason->delete();

        return Response::success(['message' => 'Reason with ID ' . $reason->id . ' deleted']);
    }
}