<?php
namespace App\Services\Termination;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\Termination;
use App\Models\User;

class TerminationService
{
    // Create termination
    public function createTermination($request)
    {
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
    }

    // Fetch termination
    public function fetchTermination($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Fetch Termination
        $termination = Termination::where('user_id', $user->id)->latest()->first();

        // Return success response
        return Response::success([
            'termination' => $termination,
        ]);
    }

    // Update termination
    public function updateTermination($request)
    {
        // Allowed fields
        $allowedFields = [
            'date',
            'reason',
            'detail',
            'is_exit_interview_performed',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get termination
        $termination = Termination::findOrFail($request->termination);

        // Update termination
        $terminationUpdated = UpdateService::updateModel($termination, $request->validated(), 'termination');

        // Return fail response in-case model is not updated
        if (!$terminationUpdated) {
            throw new \Exception(ResponseMessage::customMessage('Something went wrong. Cannot update Termination'));
        }

        // Return success response
        return Response::success([
            'termination' => $termination->latest('updated_at')->first(),
        ]);
    }

    // Delete termination
    public function deleteTermination($request)
    {
        // Get termination
        $termination = Termination::findOrFail($request->termination);

        // Delete termination
        $termination->delete();

        // Return success response
        return Response::success([
            'message' => ResponseMessage::deleteSuccess('Termination'),
        ]);
    }
}
