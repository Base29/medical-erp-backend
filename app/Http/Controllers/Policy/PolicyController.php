<?php

namespace App\Http\Controllers\Policy;

use App\Helpers\FileUpload;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Policy\CreatePolicyRequest;
use App\Models\Policy;
use App\Models\Practice;

class PolicyController extends Controller
{
    // Method for fetching policies
    public function fetch()
    {
        // Fetching policies
        $policies = Policy::with('signatures.user')->get();

        return Response::success(['policies' => $policies]);
    }

    public function create(CreatePolicyRequest $request)
    {

        // Check if the practice exists
        $practice = Practice::find($request->practice);

        // Upload policy document
        $attachment_url = FileUpload::upload($request->file('attachment'), 'policies', 's3');

        // Create Policy
        $policy = new Policy();
        $policy->name = $request->name;
        $policy->attachment = $attachment_url;
        $policy->practice_id = $practice->id;
        $policy->save();

        return Response::success(['policy' => $policy]);
    }

    public function delete($id)
    {
        // Check if practice exists
        $policy = Policy::find($id);

        if (!$policy) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Policy', $id, false),
                'code' => 404,
            ]);
        }

        // Deleting practice
        $policy->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Policy')]);
    }
}