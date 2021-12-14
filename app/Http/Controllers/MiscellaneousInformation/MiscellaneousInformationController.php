<?php

namespace App\Http\Controllers\MiscellaneousInformation;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\MiscellaneousInformation\CreateMiscellaneousInformationRequest;
use App\Http\Requests\MiscellaneousInformation\DeleteMiscellaneousInformationRequest;
use App\Http\Requests\MiscellaneousInformation\FetchMiscellaneousInformationRequest;
use App\Models\Equipment;
use App\Models\JobSpecification;
use App\Models\MiscellaneousInformation;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class MiscellaneousInformationController extends Controller
{
    // Create Miscellaneous Information

    public function create(CreateMiscellaneousInformationRequest $request)
    {
        try {

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
            $jobSpec = JobSpecification::where('id', $request->job_description)->firstOrFail();

            // Create misc info
            $miscInfo = new MiscellaneousInformation();
            $miscInfo->job_description = $jobSpec->title;
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

            return Response::success([
                'misc-info' => $miscInfo,
            ]);

        } catch (\Exception$e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch misc-info for a single user
    public function fetchSingle(FetchMiscellaneousInformationRequest $request)
    {
        try {

            // Get user
            $user = User::findOrFail($request->user);

            // Get misc-info for the user $user->id
            $miscInfo = MiscellaneousInformation::where('user_id', $user->id)->first();

            // Return response
            return Response::success([
                'misc-info' => $miscInfo,
            ]);

        } catch (\Exception$e) {
            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete single misc-info
    public function delete(DeleteMiscellaneousInformationRequest $request)
    {
        try {

            // Get user
            $user = User::findOrFail($request->user);

            // Assemble user's folder name to be deleted
            $userFolder = 'misc-info/user-' . $user->id . '/';

            // Delete misc-info folder of user on S3
            Storage::disk('s3')->deleteDirectory($userFolder);

            // Delete misc info
            $user->miscInfo()->delete();

            // Return success response
            return Response::success([
                'message' => ResponseMessage::deleteSuccess('Misc Info'),
            ]);

        } catch (\Exception$e) {
            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}