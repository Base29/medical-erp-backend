<?php

namespace App\Http\Controllers\Policy;

use App\Helpers\CustomPagination;
use App\Helpers\CustomValidation;
use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    // Method for fetching policies
    public function fetch()
    {
        // Fetching policies
        $policies = Policy::with('signatures')->get();

        $policies_arr = [];

        foreach ($policies as $policy) {
            // Structure policy object
            $policy_data = [
                'id' => $policy->id,
                'practice_id' => $policy->practice_id,
                'name' => $policy->name,
                'attachment' => $policy->attachment,
                'created_at' => $policy->created_at,
                'updated_at' => $policy->updated_at,
                'signatures' => $this->signature_data($policy->signatures),
            ];

            // Pushing $policy_data object to $policy_arr
            array_push($policies_arr, $policy_data);
        }

        // Compiling a custom collection for policies
        $policies_collection = collect($policies_arr);

        // Applying pagination to custom policies collection $policies_collection
        $policies_paginated = CustomPagination::paginate(
            $policies_collection,
            $perPage = 10,
        )->setPath(route('policies'));

        return response([
            'success' => true,
            'policies' => $policies_paginated,
        ], 200);
    }

    // Method for getting user data for the signatures
    private function signature_data($signatures)
    {
        $signature_arr = [];
        foreach ($signatures as $signature) {
            // Fetch user data for signature
            $user = User::find($signature->user_id);

            // Structure signature object
            $signature_data = [
                'id' => $signature->id,
                'user_id' => $signature->user_id,
                'signatory_name' => $user->name,
                'policy_id' => $signature->policy_id,
                'comment' => $signature->comment,
                'comment_visible' => $signature->comment_visible,
                'confirmation' => $signature->confirmation,
                'created_at' => $signature->created_at,
                'updated_at' => $signature->updated_at,
                'deleted_at' => $signature->deleted_at,
            ];

            // Pushing $signature_data object to $signature_arr
            array_push($signature_arr, $signature_data);

        }

        return $signature_arr;
    }

    public function create(Request $request)
    {
        ray($request->all());

        // Validation rules
        $rules = [
            'name' => 'required',
            'attachment' => 'required|file|mimes:doc,docx,pdf',
            'practice' => 'required|numeric',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if the practice exists
        $practice = Practice::find($request->practice);

        if (!$practice) {
            return response([
                'success' => false,
                'message' => 'Practice with the ID ' . $request->practice . ' not found',
            ], 404);
        }

        // Check if the policy exists
        $policy_exists = Policy::where('name', $request->name)->first();

        if ($policy_exists) {
            return response([
                'success' => false,
                'message' => 'Policy with the name ' . $request->name . ' already exists',
            ], 409);
        }

        // Upload policy document
        $attachment_url = FileUpload::upload($request->file('attachment'), 'policies', 's3');

        // Create Policy
        $policy = new Policy();
        $policy->name = $request->name;
        $policy->attachment = $attachment_url;
        $policy->practice_id = $practice->id;
        $policy->save();

        return response([
            'success' => true,
            'policy' => $policy,
        ], 200);
    }

    public function delete($id)
    {
        // Check if practice exists
        $policy = Policy::find($id);

        if (!$policy) {
            return response([
                'success' => false,
                'message' => 'Policy with the provided id ' . $id . ' doesn\'t exists',
            ], 404);
        }

        // Deleting practice
        $policy->delete();

        return response([
            'success' => true,
            'message' => 'Policy deleted successfully',
        ], 200);
    }
}