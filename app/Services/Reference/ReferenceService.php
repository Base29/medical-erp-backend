<?php
namespace App\Services\Reference;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\Reference;
use App\Models\User;

class ReferenceService
{
    // Create Reference
    public function createReference($request)
    {
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
        $reference->referee_email = $request->referee_email;
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
    }

    // Fetch references
    public function fetchReferences($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Get user's references
        $references = Reference::where('user_id', $user->id)->latest()->paginate(10);

        // Return success response
        return Response::success([
            'references' => $references,
        ]);
    }

    // Delete reference
    public function deleteReference($request)
    {
        // Get Reference
        $reference = Reference::findOrFail($request->reference);

        // Delete REference
        $reference->delete();

        // Return success response
        return Response::success([
            'message' => ResponseMessage::deleteSuccess('Reference'),
        ]);
    }

    // Update reference
    public function updateReference($request)
    {
        // Allowed Fields
        $allowedFields = [
            'reference_type',
            'referee_name',
            'company_name',
            'relationship',
            'referee_job_title',
            'phone_number',
            'referee_email',
            'start_date',
            'end_date',
            'can_contact_referee',
            'reference_document',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get Reference
        $reference = Reference::findOrFail($request->reference);

        // Casting $request->all() to $updateRequestData
        $updateRequestData = $request->all();

        // Initiate a null variable $referenceDocUrl
        $referenceDocUrl = null;

        // Path for uploading reference document
        $folderPath = 'reference-documents/user-' . $reference->user_id;

        // Check if request has a file
        if ($request->hasFile('reference_document')) {

            // Upload file
            $referenceDocUrl = FileUploadService::upload($request->file('reference_document'), $folderPath, 's3');

            // overriding value of reference_document with file URL
            $updateRequestData['reference_document'] = $referenceDocUrl;
        }

        // Update Reference
        $referenceUpdated = UpdateService::updateModel($reference, $updateRequestData, 'reference');

        if (!$referenceUpdated) {

            throw new \Exception(ResponseMessage::customMessage('Something went wrong. Cannot update Reference'));
        }

        // Return success response
        return Response::success([
            'reference' => $reference->latest('updated_at')->first(),
        ]);
    }
}