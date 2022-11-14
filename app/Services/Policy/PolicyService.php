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

        // Folder name
        $folderName = $request->has('type') ? $request->type . '-policies' : 'policies';

        // Upload policy document
        $attachmentUrl = FileUploadService::upload($request->file('attachment'), $folderName, 's3');

        // If upload fails
        if (!$attachmentUrl) {
            throw new \Exception(ResponseMessage::customMessage('Something went wrong while uploading document'));
        }

        // Create Policy
        $policy = new Policy();
        $policy->name = $request->name;
        $policy->description = $request->description;
        $policy->type = $request->type;
        $policy->attachment = $attachmentUrl;
        $policy->practice_id = $practice->id;
        $policy->save();

        // Check if $request has roles
        if ($request->has('roles')) {

            // Cast $request->roles to $roles variable
            $roles = $request->roles;

            // Iterate through $roles array and attach policy
            foreach ($roles as $role):
                $role->policies()->attach($policy->id);
            endforeach;
        }

        // Return success response
        return Response::success([
            'policy' => $policy->with(['roles'])->first(),
        ]);
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