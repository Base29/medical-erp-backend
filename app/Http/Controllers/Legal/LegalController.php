<?php

namespace App\Http\Controllers\Legal;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Legal\CreateLegalRequest;
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
}