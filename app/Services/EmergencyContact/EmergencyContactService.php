<?php
namespace App\Services\EmergencyContact;

use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\EmergencyContact;
use App\Models\User;

class EmergencyContactService
{
    // Create emergency contact
    public function createEmergencyContact($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Instance of EmergencyContact model
        $emergencyContact = new EmergencyContact();
        $emergencyContact->name = $request->name;
        $emergencyContact->relationship = $request->relationship;
        $emergencyContact->primary_phone = $request->primary_phone;
        $emergencyContact->secondary_phone = $request->secondary_phone;
        $user->emergencyContacts()->save($emergencyContact);

        // Return emergency contact
        return $emergencyContact;
    }

    // Fetch user emergency contacts
    public function fetchEmergencyContacts($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Get $user Emergency Contacts
        return EmergencyContact::where('user_id', $user->id)->latest()->get();
    }

    // Delete emergency contact
    public function deleteEmergencyContact($request)
    {
        // Get emergency contact
        $emergencyContact = EmergencyContact::findOrFail($request->emergency_contact);

        // Delete emergency contact
        $emergencyContact->delete();
    }

    // Update emergency contact
    public function updateEmergencyContact($request)
    {
        // Allowed fields
        $allowedFields = [
            'name',
            'relationship',
            'primary_phone',
            'secondary_phone',
            'is_primary',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get emergency contact
        $emergencyContact = EmergencyContact::findOrFail($request->emergency_contact);

        // Update emergency contact
        $emergencyContactUpdated = UpdateService::updateModel($emergencyContact, $request->all(), 'emergency_contact');

        // Return failed response in-case update fails
        if (!$emergencyContactUpdated) {
            throw new \Exception(ResponseMessage::customMessage('Something went wrong while updating emergency contact'));
        }

        // Return success response
        return $emergencyContact->latest('updated_at')->first();

    }

}