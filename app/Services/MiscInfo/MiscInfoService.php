<?php
namespace App\Services\MiscInfo;

use App\Helpers\FileUploadService;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\Equipment;
use App\Models\JobSpecification;
use App\Models\MiscellaneousInformation;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class MiscInfoService
{
    // Create Misc Info
    public function createMiscInfo($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        /**
         * Folder name for the user's misc-info
         * Format of the folder name will be user-{user_id}-misc-info
         */
        $folderName = 'misc-info/user-' . $user->id;

        // Get URLs for uploaded files
        $offerLetterEmailUrl = $request->has('offer_letter_email') ? FileUploadService::upload($request->offer_letter_email, $folderName, 's3') : null;

        $jobAdvertUrl = $request->has('job_advertisement') ? FileUploadService::upload($request->job_advertisement, $folderName, 's3') : null;

        $healthQuestionnaireUrl = $request->has('health_questionnaire') ? FileUploadService::upload($request->health_questionnaire, $folderName, 's3') : null;

        $annualDeclarationUrl = $request->has('annual_declaration') ? FileUploadService::upload($request->annual_declaration, $folderName, 's3') : null;

        $employeeConfidentialityAgreementUrl = $request->has('employee_confidentiality_agreement') ? FileUploadService::upload($request->employee_confidentiality_agreement, $folderName, 's3') : null;

        $employeePrivacyNoticeUrl = $request->has('employee_privacy_notice') ? FileUploadService::upload($request->employee_privacy_notice, $folderName, 's3') : null;

        $lockerKeyAgreementUrl = $request->has('locker_key_agreement') ? FileUploadService::upload($request->locker_key_agreement, $folderName, 's3') : null;

        $equipmentProvidedPolicyUrl = $request->has('equipment_provided_policy') ? FileUploadService::upload($request->equipment_provided_policy, $folderName, 's3') : null;

        $resumeUrl = $request->has('resume') ? FileUploadService::upload($request->resume, $folderName, 's3') : null;

        $proofOfAddressUrl = $request->has('proof_of_address') ? FileUploadService::upload($request->proof_of_address, $folderName, 's3') : null;

        // Get Job Specification
        $jobSpec = JobSpecification::where('id', $request->job_specification)->firstOrFail();

        // Create misc info
        $miscInfo = new MiscellaneousInformation();
        $miscInfo->job_specification = $jobSpec->title;
        $miscInfo->interview_notes = $request->interview_notes;
        $miscInfo->offer_letter_email = $offerLetterEmailUrl;
        $miscInfo->job_advertisement = $jobAdvertUrl;
        $miscInfo->health_questionnaire = $healthQuestionnaireUrl;
        $miscInfo->annual_declaration = $annualDeclarationUrl;
        $miscInfo->employee_confidentiality_agreement = $employeeConfidentialityAgreementUrl;
        $miscInfo->employee_privacy_notice = $employeePrivacyNoticeUrl;
        $miscInfo->locker_key_agreement = $lockerKeyAgreementUrl;
        $miscInfo->is_locker_key_assigned = $request->has('is_locker_key_assigned') ? $request->is_locker_key_assigned : 0;
        $miscInfo->equipment_provided_policy = $equipmentProvidedPolicyUrl;
        $miscInfo->resume = $resumeUrl;
        $miscInfo->proof_of_address = $proofOfAddressUrl;

        // Attach user with equipment
        foreach ($request->equipment as $equipment_id) {

            // Check if the equipment exists with the provided id $equipment_id
            $equipment = Equipment::find($equipment_id);

            if ($equipment) {
                // Check if the equipment is already assigned to the user
                $equipmentAlreadyAssigned = $user->equipment->contains('id', $equipment_id);

                if (!$equipmentAlreadyAssigned) {
                    // Attach user with the equipment whose ID is provided in the
                    $user->equipment()->attach($equipment_id);
                }
            }

        }

        // Saving misc-info
        $user->miscInfo()->save($miscInfo);

        // Return Misc Info
        return $miscInfo;
    }

    // Fetch single
    public function fetchSingleMiscInfo($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Get misc-info for the user $user->id
        return MiscellaneousInformation::where('user_id', $user->id)->first();
    }

    // Delete misc info
    public function deleteMiscInfo($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Assemble user's folder name to be deleted
        $userFolder = 'misc-info/user-' . $user->id . '/';

        // Delete misc-info folder of user on S3
        Storage::disk('s3')->deleteDirectory($userFolder);

        // Delete misc info
        $user->miscInfo()->delete();
    }

    // Update misc info
    public function updateMiscInfo($request)
    {
        // Allowed fields
        $allowedFields = [
            'job_description',
            'interview_notes',
            'offer_letter_email',
            'job_advertisement',
            'health_questionnaire',
            'annual_declaration',
            'employee_confidentiality_agreement',
            'employee_privacy_notice',
            'locker_key_agreement',
            'is_locker_key_assigned',
            'equipment_provided_policy',
            'resume',
            'proof_of_address',
            'equipment',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get Miscellaneous Information
        $miscInfo = MiscellaneousInformation::findOrFail($request->misc_info);

        // Casting request data to a variable
        $updateRequestData = $request->all();

        // Check if the request contain the ID of JobSpecification in $request->job_description
        if ($request->has('job_description')) {
            // Get Job Specification
            $jobSpec = JobSpecification::where('id', $request->job_description)->firstOrFail();

            // Override the value of $updateDataRequest['job_description'] with $jobSpec->title
            $updateRequestData['job_description'] = $jobSpec->title;
        }

        // Fields that contain files
        $fileFields = [
            'offer_letter_email',
            'job_advertisement',
            'health_questionnaire',
            'annual_declaration',
            'employee_confidentiality_agreement',
            'employee_privacy_notice',
            'locker_key_agreement',
            'equipment_provided_policy',
            'resume',
            'proof_of_address',
        ];

        // User's Miscellaneous Information folder name
        $userMiscInfoFolder = 'misc-info/user-' . $miscInfo->user_id;

        // Checking if request has any files
        if ($request->hasAny($fileFields)) {

            // Iterating through each file field
            foreach ($request->allFiles() as $fileField => $value) {

                // Checking if $fieldFile contains a file
                if ($request->hasFile($fileField)) {

                    // Overriding the value of $fileField with the url of uploaded file
                    $updateRequestData[$fileField] = $this->renderFileUrl($value, $userMiscInfoFolder);
                }
            }
        }

        // Update Misc Info
        $miscInfoUpdated = UpdateService::updateModel($miscInfo, $updateRequestData, 'misc_info');

        if (!$miscInfoUpdated) {
            throw new \Exception(ResponseMessage::customMessage('Something went wrong. Cannot update Misc Info at this time.'));
        }

        // Return success response
        return $miscInfo->latest('updated_at')->first();
    }

    // Function for uploading files are returning the url
    private function renderFileUrl($file, $userMiscInfoFolder)
    {
        // Upload employee_privacy_notice
        $url = FileUploadService::upload($file, $userMiscInfoFolder, 's3');

        // Return URL of the uploaded files
        return $url;
    }
}