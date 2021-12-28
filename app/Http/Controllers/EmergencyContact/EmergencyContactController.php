<?php

namespace App\Http\Controllers\EmergencyContact;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmergencyContact\CreateEmergencyContactRequest;
use App\Http\Requests\EmergencyContact\DeleteEmergencyContactRequest;
use App\Http\Requests\EmergencyContact\FetchEmergencyContactRequest;
use App\Http\Requests\EmergencyContact\UpdateEmergencyContactRequest;
use App\Models\EmergencyContact;
use App\Models\User;

class EmergencyContactController extends Controller
{
    // Create emergency contact
    public function create(CreateEmergencyContactRequest $request)
    {
        try {
            // Get user
            $user = User::findOrFail($request->user);

            // Instance of EmergencyContact model
            $emergencyContact = new EmergencyContact();
            $emergencyContact->name = $request->name;
            $emergencyContact->relationship = $request->relationship;
            $emergencyContact->primary_phone = $request->primary_phone;
            $emergencyContact->secondary_phone = $request->secondary_phone;
            $user->emergencyContacts()->save($emergencyContact);

            // Return success response
            return Response::success([
                'emergency-contact' => $emergencyContact,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch user's emergency contacts
    public function fetch(FetchEmergencyContactRequest $request)
    {
        try {
            // Get user
            $user = User::findOrFail($request->user);

            // Get $user Emergency Contacts
            $emergencyContacts = EmergencyContact::where('user_id', $user->id)->latest()->get();

            // Return success response
            return Response::success([
                'emergency-contacts' => $emergencyContacts,
            ]);
        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete emergency contact
    public function delete(DeleteEmergencyContactRequest $request)
    {
        try {
            // Get emergency contact
            $emergencyContact = EmergencyContact::findOrFail($request->emergency_contact);

            // Delete emergency contact
            $emergencyContact->delete();

            // Return success response
            return Response::success([
                'message' => ResponseMessage::deleteSuccess('Emergency Contact'),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update emergency contact
    public function update(UpdateEmergencyContactRequest $request)
    {
        try {
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
                return Response::fail([
                    'message' => ResponseMessage::allowedFields($allowedFields),
                    'code' => 400,
                ]);
            }

            // Get emergency contact
            $emergencyContact = EmergencyContact::findOrFail($request->emergency_contact);

            // Update emergency contact
            $emergencyContactUpdated = UpdateService::updateModel($emergencyContact, $request->all(), 'emergency_contact');

            // Return failed response in-case update fails
            if (!$emergencyContactUpdated) {
                return Response::fail([
                    'code' => 400,
                    'message' => ResponseMessage::customMessage('Something went wrong while updating emergency contact'),
                ]);
            }

            // Return success response
            return Response::success([
                'emergency-contact' => $emergencyContact->latest('updated_at')->first(),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}