<?php

namespace App\Http\Controllers\Reference;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reference\CreateReferenceRequest;
use App\Http\Requests\Reference\DeleteReferenceRequest;
use App\Http\Requests\Reference\FetchReferenceRequest;
use App\Models\Reference;
use App\Models\User;

class ReferenceController extends Controller
{
    // Create reference
    public function create(CreateReferenceRequest $request)
    {
        try {

            // Get user
            $user = User::findOrFail($request->user);

            // Initiate a null variable $referenceDocUrl
            $referenceDocUrl = null;

            // Path for uploading reference document
            $folderPath = 'reference-documents/user-' . $user->id;

            // Check if $request->reference_document is present
            if ($request->hasFile('reference_document')) {
                $referenceDocUrl = FileUploadService::upload($request->file('reference_document'), $folderPath, 's3');
            }

            // Initiate instance of Reference model
            $reference = new Reference();
            $reference->reference_type = $request->reference_type;
            $reference->referee_name = $request->referee_name;
            $reference->company_name = $request->company_name;
            $reference->relationship = $request->relationship;
            $reference->referee_job_title = $request->referee_job_title;
            $reference->phone_number = $request->phone_number;
            $reference->start_date = $request->start_date;
            $reference->end_date = $request->end_date;
            $reference->can_contact_referee = $request->can_contact_referee;
            $reference->reference_document = $referenceDocUrl;

            // Save reference for the user
            $user->references()->save($reference);

            // Return success response
            return Response::success([
                'reference' => $reference,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch user's all references
    public function fetch(FetchReferenceRequest $request)
    {
        try {

            // Get user
            $user = User::findOrFail($request->user);

            // Get user's references
            $references = Reference::where('user_id', $user->id)->latest()->paginate(10);

            // Return success response
            return Response::success([
                'references' => $references,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete user reference
    public function delete(DeleteReferenceRequest $request)
    {
        try {
            //
        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
