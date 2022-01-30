<?php
namespace App\Services\Legal;

use App\Helpers\FileUploadService;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\GmcSpecialistRegister;
use App\Models\Legal;
use App\Models\NmcQualification;
use App\Models\User;
use Illuminate\Support\Arr;

class LegalService
{
    // Create legal
    public function createLegal($request)
    {
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
        return $legal->with('nmcQualifications', 'gmcSpecialistRegisters')->latest()->first();
    }

    // Fetch single legal
    public function fetchSingleLegal($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Get user's legal data
        return Legal::where('user_id', $user->id)->with('nmcQualifications', 'gmcSpecialistRegisters')->latest()->first();
    }

    // Delete legal
    public function deleteLegal($request)
    {
        // Get legal
        $legal = Legal::findOrFail($request->legal);

        // Delete Legal
        $legal->delete();
    }

    // Update legal
    public function updateLegal($request)
    {
        // Get legal
        $legal = Legal::where('id', $request->legal)->with('nmcQualifications', 'gmcSpecialistRegisters')->first();

        // Cast update request data to a variable
        $updateRequestData = null;

        // If is_nurse = true
        if ($legal->is_nurse) {

            // Check if request has gmc nmc qualifications array
            if ($request->has('nmc_qualifications')) {
                $this->updateSubItems($request->nmc_qualifications, 'nmc');
            }

            // Allowed fields for NMC
            $allowedFieldsNmc = [
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
                throw new \Exception(ResponseMessage::allowedFields($allowedFieldsNmc));
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

            // Check if request has gmc specialist registers array
            if ($request->has('gmc_specialist_registers')) {
                $this->updateSubItems($request->gmc_specialist_registers, 'gmc');
            }

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
                throw new \Exception(ResponseMessage::allowedFields($allowedFieldsGmc));
            }

            $updateRequestData = $request->only($allowedFieldsGmc);

        }

        // Update legal
        $legalUpdated = UpdateService::updateModel($legal, $updateRequestData, 'legal');

        // Return fail response if something went wrong while updating
        if (!$legalUpdated) {
            throw new \Exception(ResponseMessage::customMessage('Something went wrong while updating Legal.'));
        }

        // Return success response
        return $legal->with('nmcQualifications', 'gmcSpecialistRegisters')->latest('updated_at')->first();
    }

    // Updating sub items
    private function updateSubItems($subItems, $tag)
    {

        // Loop through the $subItems array
        foreach ($subItems as $subItem) {

            // Allowed fields
            $allowedFields = [
                'name',
                'date',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!Arr::hasAny($subItem, $allowedFields)) {
                throw new \Exception(ResponseMessage::allowedFields($allowedFields));
            }

            // Get model depending on provided $tag gmc or nmc
            $model = $tag === 'gmc' ? GmcSpecialistRegister::findOrFail($subItem['id']) : NmcQualification::findOrFail($subItem['id']);

            // Update subitem
            UpdateService::updateModel($model, $subItem, 'id');

        }

    }
}