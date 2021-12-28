<?php

namespace App\Http\Controllers\Termination;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Termination\CreateTerminationRequest;
use App\Models\Termination;
use App\Models\User;

class TerminationController extends Controller
{
    // Create termination
    public function create(CreateTerminationRequest $request)
    {
        try {
            // Get user
            $user = User::findOrFail($request->user);

            // Instance of Termination
            $termination = new Termination();
            $termination->date = $request->date;
            $termination->reason = $request->reason;
            $termination->detail = $request->detail;
            $termination->is_exit_interview_performed = $request->is_exit_interview_performed;

            // Save termination for $user
            $user->termination()->save($termination);

            // Return success response
            return Response::success([
                'termination' => $termination,
            ]);
        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}