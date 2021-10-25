<?php

namespace App\Http\Controllers\Reason;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reason\CreateReasonRequest;
use App\Models\Reason;

class ReasonController extends Controller
{

    // Create Reason
    public function create(CreateReasonRequest $request)
    {
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
                'message' => ResponseMessage::notFound('Reason', $id, false),
                'code' => 404,
            ]);
        }

        // Delete reason
        $reason->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Reason')]);
    }
}