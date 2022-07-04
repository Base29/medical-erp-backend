<?php
namespace App\Services\ItPolicy;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Models\ItPolicy;
use App\Models\Role;

class ItPolicyService
{
    public function createItPolicy($request)
    {
        // Set folder path
        $folderPath = 'it-policies';

        // Upload employee handbook
        $itPolicyUrl = FileUploadService::upload($request->it_policy, $folderPath, 's3');

        // Initiate instance of EmployeeHandbook model
        $itPolicy = new ItPolicy();
        $itPolicy->detail = $request->detail;
        $itPolicy->url = $itPolicyUrl;
        $itPolicy->save();

        // Return success response
        return Response::success([
            'it-policy' => $itPolicy->with('roles')->latest()->first(),
        ]);
    }

    // Fetch all it policies
    public function fetchItPolicies()
    {
        // Get It policies
        $itPolicies = ItPolicy::with('roles')->latest()->paginate(10);

        // Return success response
        return Response::success([
            'it-policies' => $itPolicies,
        ]);
    }

    public function deleteItPolicy($request)
    {
        // Get employee handbook
        $itPolicy = ItPolicy::findOrFail($request->it_policy);

        // Delete
        $itPolicy->delete();

        // Return Response
        return Response::success([
            'it-policy' => $itPolicy,
        ]);
    }

    // Fetch single
    public function fetchSingleItPolicy($request)
    {
        // Get It Policy
        $itPolicy = ItPolicy::where('id', $request->it_policy)
            ->with('roles')
            ->firstOrFail();

        // Return response
        return Response::success([
            'it-policy' => $itPolicy,
        ]);
    }

    // Sign It policy
    public function signItPolicies($request)
    {
        // Authenticated user
        $user = auth()->user();

        // Get role
        $role = Role::findOrFail($request->role);

        // Cast $role->itPolicies to variable
        $itPolicies = $role->itPolicies;

        // Loop through it policies and sign them
        foreach ($itPolicies as $itPolicy) {
            $itPolicy->signatures()->attach($user->id);
        }

        return Response::success([
            'user' => $user->where('id', $user->id)->with('signedItPolicies')->firstOrFail(),
        ]);
    }
}