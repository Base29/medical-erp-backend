<?php

namespace App\Http\Controllers\Termination;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Termination\CreateTerminationRequest;
use App\Http\Requests\Termination\FetchTerminationRequest;
use App\Http\Requests\Termination\UpdateTerminationRequest;
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

    // Fetch user termination
    public function fetch(FetchTerminationRequest $request)
    {
        try {
            // Get user
            $user = User::findOrFail($request->user);

            // Fetch Termination
            $termination = Termination::where('user_id', $user->id)->latest()->first();

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

    // Update termination
    public function update(UpdateTerminationRequest $request)
    {
        try {
            // Allowed fields
            $allowedFields = [
                'date',
                'reason',
                'detail',
                'is_exit_interview_performed',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!$request->hasAny($allowedFields)) {
                return Response::fail([
                    'message' => ResponseMessage::allowedFields($allowedFields),
                    'code' => 400,
                ]);
            }

            // Get termination
            $termination = Termination::findOrFail($request->termination);

            // Update termination
            $terminationUpdated = UpdateService::updateModel($termination, $request->all(), 'termination');

            // Return fail response in-case model is not updated
            if (!$terminationUpdated) {
                return Response::fail([
                    'code' => 400,
                    'message' => ResponseMessage::customMessage('Something went wrong. Cannot update Termination'),
                ]);
            }

            // Return success response
            return Response::success([
                'termination' => $termination->latest('updated_at')->first(),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}