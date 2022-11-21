<?php
namespace App\Services\Reason;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\Reason;
use Exception;

class ReasonService
{
    // Create reason
    public function createReason($request)
    {
        // Create reason
        $reason = new Reason();
        $reason->reason = $request->reason;
        $reason->save();

        return Response::success(['reason' => $reason]);
    }

    // Fetch reason
    public function fetchReasons()
    {
        // Reasons
        $reasons = Reason::latest()->paginate(10);

        return Response(['reasons' => $reasons]);
    }

    // Delete reason
    public function deleteReason($id)
    {
        // Check if the reason exists with the provided ID
        $reason = Reason::findOrFail($id);

        if (!$reason) {
            throw new Exception(ResponseMessage::notFound('Reason', $id, false), Response::HTTP_NOT_FOUND);
        }

        // Delete reason
        $reason->delete();

        return Response::success([
            'code' => Response::HTTP_OK,
            'reason' => $reason,
        ]);
    }
}