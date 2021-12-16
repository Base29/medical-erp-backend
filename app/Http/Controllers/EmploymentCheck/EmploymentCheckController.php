<?php

namespace App\Http\Controllers\EmploymentCheck;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmploymentCheck\CreateEmploymentCheckRequest;
use App\Http\Requests\EmploymentCheck\DeleteEmploymentCheckRequest;
use App\Http\Requests\EmploymentCheck\FetchSingleEmploymentCheckRequest;
use App\Http\Requests\EmploymentCheck\UpdateEmploymentCheckRequest;
use App\Models\EmploymentCheck;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class EmploymentCheckController extends Controller
{
    // Create employment check
    public function create(CreateEmploymentCheckRequest $request)
    {
        try {

            // Get user
            $user = User::findOrFail($request->user);

            // DBS self declared certificate folder name
            $selfDeclaredCertificateFolder = 'employment-check/user-' . $user->id . '/dbs-self-declared';

            // DBS certificate folder name
            $dbsCertificateFolder = 'employment-check/user-' . $user->id . '/dbs-certificate';

            // Right to work
            $rightToWorkCertificateFolder = 'employment-check/user-' . $user->id . '/right-to-work-certificate';

            // Upload self declared dbs certificate
            $selfDeclaredCertificateUrl = $request->hasFile('self_declaration_certificate') ? FileUploadService::upload($request->self_declaration_certificate, $selfDeclaredCertificateFolder, 's3') : null;

            // Upload dbs certificate
            $dbsCertificateUrl = $request->hasFile('dbs_certificate') ? FileUploadService::upload($request->dbs_certificate, $dbsCertificateFolder, 's3') : null;

            // Upload right to work certificate
            $rightToWorkCertificateUrl = $request->hasFile('right_to_work_certificate') ? FileUploadService::upload($request->right_to_work_certificate, $rightToWorkCertificateFolder, 's3') : null;

            // Create user nationality
            $employmentCheck = new EmploymentCheck();
            $employmentCheck->passport_number = $request->passport_number;
            $employmentCheck->passport_country_of_issue = $request->passport_country_of_issue;
            $employmentCheck->passport_date_of_expiry = $request->passport_date_of_expiry;
            $employmentCheck->is_uk_citizen = $request->is_uk_citizen;
            $employmentCheck->right_to_work_status = $request->right_to_work_status;
            $employmentCheck->right_to_work_certificate = $rightToWorkCertificateUrl;
            $employmentCheck->share_code = $request->share_code;
            $employmentCheck->date_issued = $request->date_issued;
            $employmentCheck->date_checked = $request->date_checked;
            $employmentCheck->expiry_date = $request->expiry_date;
            $employmentCheck->visa_required = $request->visa_required;
            $employmentCheck->visa_number = $request->visa_number;
            $employmentCheck->visa_start_date = $request->visa_start_date;
            $employmentCheck->visa_expiry_date = $request->visa_expiry_date;
            $employmentCheck->restrictions = $request->restrictions;
            $employmentCheck->is_dbs_required = $request->is_dbs_required;
            $employmentCheck->self_declaration_completed = $request->self_declaration_completed;
            $employmentCheck->self_declaration_certificate = $selfDeclaredCertificateUrl;
            $employmentCheck->is_dbs_conducted = $request->is_dbs_conducted;
            $employmentCheck->dbs_conducted_date = $request->dbs_conducted_date;
            $employmentCheck->follow_up_date = $request->follow_up_date;
            $employmentCheck->dbs_certificate = $dbsCertificateUrl;
            $employmentCheck->dbs_certificate_number = $request->dbs_certificate_number;
            $employmentCheck->driving_license_number = $request->driving_license_number;
            $employmentCheck->driving_license_country_of_issue = $request->driving_license_country_of_issue;
            $employmentCheck->driving_license_class = $request->driving_license_class;
            $employmentCheck->driving_license_date_of_expiry = $request->driving_license_date_of_expiry;

            // Save employment checks
            $user->employmentCheck()->save($employmentCheck);

            // Return response
            return Response::success([
                'employment_check' => $employmentCheck,
            ]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update employment check
    public function update(UpdateEmploymentCheckRequest $request)
    {
        try {

            // Allowed fields
            $allowedFields = [
                'passport_number',
                'passport_country_of_issue',
                'passport_date_of_expiry',
                'is_uk_citizen',
                'right_to_work_status',
                'right_to_work_certificate',
                'share_code',
                'date_issued',
                'date_checked',
                'expiry_date',
                'visa_required',
                'visa_number',
                'visa_start_date',
                'visa_expiry_date',
                'restrictions',
                'is_dbs_required',
                'self_declaration_completed',
                'self_declaration_certificate',
                'is_dbs_conducted',
                'dbs_conducted_date',
                'follow_up_date',
                'dbs_certificate',
                'dbs_certificate_number',
                'driving_license_number',
                'driving_license_country_of_issue',
                'driving_license_class',
                'driving_license_date_of_expiry',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!$request->hasAny($allowedFields)) {
                return Response::fail([
                    'message' => ResponseMessage::allowedFields($allowedFields),
                    'code' => 400,
                ]);
            }

            // Get employment check
            $employmentCheck = EmploymentCheck::findOrFail($request->employment_check);

            // Mapping $request->all() to a variable
            $updateRequestData = $request->all();

            //TODO: File url update functionality needs to be changed/enhanced as done in MiscellaneousInformationController
            // Check if request contains files to upload
            if ($request->hasAny(['self_declaration_certificate', 'dbs_certificate', 'right_to_work_certificate'])) {

                // If self_declaration_certificate file is provided
                if ($request->hasFile('self_declaration_certificate')) {

                    // DBS self declared certificate folder name
                    $selfDeclaredCertificateFolder = 'employment-check/user-' . $employmentCheck->user_id . '/dbs-self-declared';

                    // // Get path of the old file from the URL saved in the database
                    // $oldFile = parse_url($employmentCheck->self_declaration_certificate);

                    // // Delete existing file
                    // Storage::disk('s3')->delete($oldFile['path']);

                    // Upload self declared dbs certificate
                    $selfDeclaredCertificateUrl = FileUploadService::upload($request->self_declaration_certificate, $selfDeclaredCertificateFolder, 's3');

                    // Overriding values of self_declaration_certificate field with the url of uploaded file.
                    $updateRequestData['self_declaration_certificate'] = $selfDeclaredCertificateUrl;

                }

                // If dbs_certificate file is provided
                if ($request->hasFile('dbs_certificate')) {
                    //DBS certificate folder name
                    $dbsCertificateFolder = 'employment-check/user-' . $employmentCheck->user_id . '/dbs-certificate';

                    // // Get path of the old file from the URL saved in the database
                    // $oldFile = parse_url($employmentCheck->dbs_certificate);

                    // // Delete existing file
                    // Storage::disk('s3')->delete($oldFile['path']);

                    // Upload dbs certificate
                    $dbsCertificateUrl = FileUploadService::upload($request->dbs_certificate, $dbsCertificateFolder, 's3');

                    // Overriding values of dbs_certificate field with the urls of uploaded files.
                    $updateRequestData['dbs_certificate'] = $dbsCertificateUrl;
                }

                // If right_to_work_certificate file is provided
                if ($request->hasFile('right_to_work_certificate')) {
                    // Right to work certificate folder name
                    $rightToWorkCertificateFolder = 'employment-check/user-' . $employmentCheck->user_id . '/right-to-work-certificate';

                    // Upload Right to work certificate
                    $rightToWorkCertificateUrl = FileUploadService::upload($request->right_to_work_certificate, $rightToWorkCertificateFolder, 's3');

                    // Overriding values of right_to_work_certificate field with the urls of uploaded files.
                    $updateRequestData['right_to_work_certificate'] = $rightToWorkCertificateUrl;
                }

            }

            // Update employment check
            $employmentCheckUpdated = UpdateService::updateModel($employmentCheck, $updateRequestData, 'employment_check');

            if (!$employmentCheckUpdated) {
                return Response::fail([
                    'code' => 400,
                    'message' => ResponseMessage::customMessage('Something went wrong. Cannot update Employment Check at this moment.'),
                ]);
            }

            // Return success response
            return Response::success([
                'employment_check' => $employmentCheck->latest('updated_at')->first(),
            ]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete empolyment check
    public function delete(DeleteEmploymentCheckRequest $request)
    {
        try {

            // Get user
            $user = User::findOrFail($request->user);

            // Assemble user's folder name to be deleted
            $userFolder = 'employment-check/user-' . $user->id . '/';

            // Delete employment-check folder of user on S3
            Storage::disk('s3')->deleteDirectory($userFolder);

            // Delete employment check from DB
            $user->employmentCheck()->delete();

            // Return success response
            return Response::success([
                'message' => ResponseMessage::deleteSuccess('Employment Check'),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Get single employment check
    public function fetchSingle(FetchSingleEmploymentCheckRequest $request)
    {
        try {

            // Get user
            $user = User::findOrFail($request->user);

            // Return response
            return Response::success([
                'employment-check' => $user->employmentCheck,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}