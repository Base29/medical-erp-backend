<?php
namespace App\Services\Policy;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\Policy;
use App\Models\Practice;

class PolicyService
{
    // Create policy
    public function createPolicy($request)
    {
        // Check if the practice exists
        $practice = Practice::findOrFail($request->practice);

        // Upload policy document
        $attachmentUrl = FileUploadService::upload($request->file('attachment'), 'policies', 's3');

        // Create Policy
        $policy = new Policy();
        $policy->name = $request->name;
        $policy->attachment = $attachmentUrl;
        $policy->practice_id = $practice->id;
        $policy->save();

        return Response::success(['policy' => $policy]);
    }

    // Fetch Policies
    public function fetchPolicies()
    {
        // Fetching policies
        $policies = Policy::with('signatures.user')->latest()->get();

        return Response::success(['policies' => $policies]);
    }

    // Delete policy
    public function deletePolicy($id)
    {
        // Check if practice exists
        $policy = Policy::findOrFail($id);

        if (!$policy) {
            throw new \Exception(ResponseMessage::notFound('Policy', $id, false));
        }

        // Deleting practice
        $policy->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Policy')]);
    }
}