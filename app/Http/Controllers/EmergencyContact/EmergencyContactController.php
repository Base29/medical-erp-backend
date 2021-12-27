<?php

namespace App\Http\Controllers\EmergencyContact;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmergencyContact\CreateEmergencyContactRequest;
use App\Http\Requests\EmergencyContact\FetchEmergencyContactRequest;
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
}