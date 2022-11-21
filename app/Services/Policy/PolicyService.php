<?php
namespace App\Services\Policy;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\Policy;
use App\Models\Practice;
use Exception;

class PolicyService
{
    // Create policy
    public function createPolicy($request)
    {
        // If $request has practice
        if ($request->has('practice')) {

            // Check if the practice exists
            $practice = Practice::findOrFail($request->practice);

        }

        // Folder name
        $folderName = $request->has('type') ? 'policies/' . $request->type . '-policies' : 'policies';

        // Upload policy document
        $attachmentUrl = FileUploadService::upload($request->file('attachment'), $folderName, 's3');

        // If upload fails
        if (!$attachmentUrl) {
            throw new Exception(ResponseMessage::customMessage('Something went wrong while uploading document'), Response::HTTP_BAD_REQUEST);
        }

        // Create Policy
        $policy = new Policy();
        $policy->name = $request->name;
        $policy->description = $request->description;
        $policy->type = $request->type;
        $policy->attachment = $attachmentUrl;
        $policy->practice_id = isset($practice) ? $practice->id : null;
        $policy->save();

        // Check if $request has roles
        if ($request->has('roles')) {

            // Cast $request->roles to $roles variable
            $roles = $request->roles;

            // Iterate through $roles array and attach policy
            foreach ($roles as $role):
                $policy->roles()->attach($role['role']);
            endforeach;
        }

        // Return success response
        return Response::success([
            'code' => Response::HTTP_CREATED,
            'policy' => $policy->with(['roles'])->latest()->first(),
        ]);
    }

    // Fetch Policies
    public function fetchPolicies()
    {
        // Fetching policies
        $policies = Policy::with('signatures.user')->latest()->get();

        return Response::success([
            'code' => Response::HTTP_OK,
            'policies' => $policies,
        ]);
    }

    // Delete policy
    public function deletePolicy($id)
    {
        // Check if practice exists
        $policy = Policy::findOrFail($id);

        if (!$policy) {
            throw new Exception(ResponseMessage::notFound('Policy', $id, false), Response::HTTP_NOT_FOUND);
        }

        // Deleting practice
        $policy->delete();

        return Response::success([
            'code' => Response::HTTP_OK,
            'policy' => $policy,
        ]);
    }
}