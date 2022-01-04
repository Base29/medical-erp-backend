<?php

namespace App\Http\Controllers\Legal;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Legal\CreateLegalRequest;
use App\Http\Requests\Legal\DeleteLegalRequest;
use App\Http\Requests\Legal\FetchLegalRequest;
use App\Http\Requests\Legal\UpdateLegalRequest;
use App\Models\GmcSpecialistRegister;
use App\Models\Legal;
use App\Models\NmcQualification;
use App\Models\User;

class LegalController extends Controller
{
    // Create Legal
    public function create(CreateLegalRequest $request)
    {
        try {
            // Get user
            $user = User::findOrFail($request->user);

            // Instance of Legal model
            $legal = new Legal();

            // Check value of boolean $request->is_nurse is true
            if ($request->is_nurse) {

                // Initiate a null variable
                $nmcDocumentUrl = null;

                // Check if request has file $request->nmc_document
                if ($request->hasFile('nmc_document')) {
                    // Folder path for NMC Document
                    $folderPath = 'legal/user-' . $user->id . '/nmc-document';

                    // Upload NMC document and get the file url
                    $nmcDocumentUrl = FileUploadService::upload($request->file('nmc_document'), $folderPath, 's3');
                }

                // If is_nurse is true
                $legal->is_nurse = $request->is_nurse;
                $legal->name = $request->name;
                $legal->location = $request->location;
                $legal->expiry_date = $request->expiry_date;
                $legal->registration_status = $request->registration_status;
                $legal->register_entry = $request->register_entry;
                $legal->register_entry_date = $request->register_entry_date;
                $legal->nmc_document = $nmcDocumentUrl;

                // Save legal with NMC
                $user->legal()->save($legal);

                // If request has nmc_qualifications array
                if ($request->has('nmc_qualifications')) {
                    $qualifications = $request->nmc_qualifications;
                    foreach ($qualifications as $qualification) {
                        // Instance of NmcQualifications model
                        $nmcQualification = new NmcQualification();
                        $nmcQualification->name = $qualification['name'];
                        $nmcQualification->date = $qualification['date'];

                        // Save NMC Qualification
                        $legal->nmcQualifications()->save($nmcQualification);
                    }

                }

            }

            // If is_nurse = false then information for GMS if stored in the database
            if (!$request->is_nurse) {

                $legal->gmc_reference_number = $request->gmc_reference_number;
                $legal->gp_register_date = $request->gp_register_date;
                $legal->provisional_registration_date = $request->provisional_registration_date;
                $legal->full_registration_date = $request->full_registration_date;

                // Save legal with GMC
                $user->legal()->save($legal);

                // If request has gmc_specialist_registers
                if ($request->has('gmc_specialist_registers')) {
                    $specialistRegisters = $request->gmc_specialist_registers;

                    foreach ($specialistRegisters as $specialistRegister) {
                        // Instance of GmcSpecialistRegister model
                        $gmcSpecialistRegister = new GmcSpecialistRegister();
                        $gmcSpecialistRegister->name = $specialistRegister['name'];
                        $gmcSpecialistRegister->date = $specialistRegister['date'];
                        $legal->gmcSpecialistRegisters()->save($gmcSpecialistRegister);
                    }
                }
            }

            // Return success response
            return Response::success([
                'legal' => $legal->with('nmcQualifications', 'gmcSpecialistRegisters')->latest()->first(),
            ]);
        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch user's legal
    public function fetch(FetchLegalRequest $request)
    {
        try {
            // Get user
            $user = User::findOrFail($request->user);

            // Get user's legal data
            $legal = Legal::where('user_id', $user->id)->with('nmcQualifications', 'gmcSpecialistRegisters')->latest()->first();

            // Return success response
            return Response::success([
                'legal' => $legal,
            ]);
        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete Legal
    public function delete(DeleteLegalRequest $request)
    {
        try {
            // Get legal
            $legal = Legal::findOrFail($request->legal);

            // Delete Legal
            $legal->delete();

            // Return success response
            return Response::success([
                'message' => ResponseMessage::deleteSuccess('Legal'),
            ]);
        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update Legal
    public function update(UpdateLegalRequest $request)
    {
        try {

            // Get legal
            $legal = Legal::findOrFail($request->legal);

            // Cast update request data to a variable
            $updateRequestData = null;

            // If is_nurse = true
            if ($legal->is_nurse) {

                // Allowed fields for NMC
                $allowedFieldsNmc = [
                    'is_nurse',
                    'name',
                    'location',
                    'expiry_date',
                    'registration_status',
                    'register_entry',
                    'register_entry_date',
                    'nmc_document',
                ];

                // Checking if the $request doesn't contain any of the allowed fields
                if (!$request->hasAny($allowedFieldsNmc)) {
                    return Response::fail([
                        'message' => ResponseMessage::allowedFields($allowedFieldsNmc),
                        'code' => 400,
                    ]);
                }

                $updateRequestData = $request->only($allowedFieldsNmc);

                // Check if request has file $request->nmc_document
                if ($request->hasFile('nmc_document')) {
                    // Folder path for NMC Document
                    $folderPath = 'legal/user-' . $legal->user_id . '/nmc-document';

                    // Upload NMC document and get the file url
                    $updateRequestData['nmc_document'] = FileUploadService::upload($request->file('nmc_document'), $folderPath, 's3');
                }

            }

            // If is_nurse = false
            if (!$legal->is_nurse) {

                // Allowed fields for GMC
                $allowedFieldsGmc = [
                    'gmc_reference_number',
                    'gp_register_date',
                    'specialist_register',
                    'provisional_registration_date',
                    'full_registration_date',
                ];

                // Checking if the $request doesn't contain any of the allowed fields
                if (!$request->hasAny($allowedFieldsGmc)) {
                    return Response::fail([
                        'message' => ResponseMessage::allowedFields($allowedFieldsGmc),
                        'code' => 400,
                    ]);
                }

                $updateRequestData = $request->only($allowedFieldsGmc);

            }

            // Update legal
            $legalUpdated = UpdateService::updateModel($legal, $updateRequestData, 'legal');

            // Return fail response if something went wrong while updating
            if (!$legalUpdated) {
                return Response::fail([
                    'code' => 400,
                    'message' => ResponseMessage::customMessage('Something went wrong while updating Legal.'),
                ]);
            }

            // Return success response
            return Response::success([
                'legal' => $legal->with('nmcQualifications', 'gmcSpecialistRegisters')->latest('updated_at')->first(),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}