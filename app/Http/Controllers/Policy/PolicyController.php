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
        try {

            // Fetching policies
            $policies = Policy::with('signatures.user')->latest()->get();

            return Response::success(['policies' => $policies]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function create(CreatePolicyRequest $request)
    {

        try {

            // Check if the practice exists
            $practice = Practice::findOrFail($request->practice);

            // Upload policy document
            $attachmentUrl = FileUpload::upload($request->file('attachment'), 'policies', 's3');

            // Create Policy
            $policy = new Policy();
            $policy->name = $request->name;
            $policy->attachment = $attachmentUrl;
            $policy->practice_id = $practice->id;
            $policy->save();

            return Response::success(['policy' => $policy]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function delete($id)
    {

        try {

            // Check if practice exists
            $policy = Policy::findOrFail($id);

            if (!$policy) {
                return Response::fail([
                    'message' => ResponseMessage::notFound('Policy', $id, false),
                    'code' => 404,
                ]);
            }

            // Deleting practice
            $policy->delete();

            return Response::success(['message' => ResponseMessage::deleteSuccess('Policy')]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}