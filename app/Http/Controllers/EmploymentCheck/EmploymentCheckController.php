<?php

namespace App\Http\Controllers\EmploymentCheck;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmploymentCheck\CreateEmploymentCheckRequest;
use App\Models\DbsCheck;
use App\Models\Nationality;
use App\Models\OtherEmploymentCheck;
use App\Models\User;

class EmploymentCheckController extends Controller
{
    // Create employment check
    public function create(CreateEmploymentCheckRequest $request)
    {
        try {

            // Get user
            $user = User::findOrFail($request->user);

            // Create user nationality
            $nationality = new Nationality();
            $nationality->passport_number = $request->passport_number;
            $nationality->passport_country_of_issue = $request->passport_country_of_issue;
            $nationality->passport_date_of_expiry = $request->passport_date_of_expiry;
            $nationality->is_uk_citizen = $request->is_uk_citizen;
            $nationality->right_to_work_status = $request->right_to_work_status;
            $nationality->share_code = $request->share_code;
            $nationality->date_issued = $request->date_issued;
            $nationality->date_checked = $request->date_checked;
            $nationality->expiry_date = $request->expiry_date;
            $nationality->visa_required = $request->visa_required;
            $nationality->visa_number = $request->visa_number;
            $nationality->visa_start_date = $request->visa_start_date;
            $nationality->visa_expiry_date = $request->visa_expiry_date;
            $nationality->restrictions = $request->restrictions;

            // DBS self declared certificate folder name
            $selfDeclaredCertificateFolder = 'employment-check/user-' . $user->id . '/dbs-self-declared';

            //DBS certificate folder name
            $dbsCertificateFolder = 'employment-check/user-' . $user->id . '/dbs-certificate';

            // Upload self declared dbs certificate
            $selfDeclaredCertificateUrl = $request->has('self_declaration_certificate') ? FileUploadService::upload($request->self_declaration_certificate, $selfDeclaredCertificateFolder, 's3') : null;

            // Upload dbs certificate
            $dbsCertificateUrl = $request->has('dbs_certificate') ? FileUploadService::upload($request->dbs_certificate, $dbsCertificateFolder, 's3') : null;

            // Create DBS
            $dbs = new DbsCheck();
            $dbs->is_dbs_required = $request->is_dbs_required;
            $dbs->self_declaration_completed = $request->self_declaration_completed;
            $dbs->self_declaration_certificate = $selfDeclaredCertificateUrl;
            $dbs->is_dbs_conducted = $request->is_dbs_conducted;
            $dbs->dbs_conducted_date = $request->dbs_conducted_date;
            $dbs->follow_up_date = $request->follow_up_date;
            $dbs->dbs_certificate = $dbsCertificateUrl;

            // Create other employment check
            $otherEmploymentCheck = new OtherEmploymentCheck();
            $otherEmploymentCheck->driving_license_number = $request->driving_license_number;
            $otherEmploymentCheck->driving_license_country_of_issue = $request->driving_license_country_of_issue;
            $otherEmploymentCheck->driving_license_class = $request->driving_license_class;
            $otherEmploymentCheck->driving_license_date_of_expiry = $request->driving_license_date_of_expiry;

            // Save employment checks
            $user->nationality()->save($nationality);
            $user->dbsCheck()->save($dbs);
            $user->otherEmploymentCheck()->save($otherEmploymentCheck);

            // Return response
            return Response::success([
                'user' => $user
                    ->with('profile', 'positionSummary', 'contractSummary', 'roles', 'practices', 'nationality', 'dbsCheck', 'otherEmploymentCheck')
                    ->latest()
                    ->first(),
            ]);

        } catch (\Exception$e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}