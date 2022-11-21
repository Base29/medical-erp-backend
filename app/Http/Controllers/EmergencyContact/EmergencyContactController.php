<?php

namespace App\Http\Controllers\EmergencyContact;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmergencyContact\CreateEmergencyContactRequest;
use App\Http\Requests\EmergencyContact\DeleteEmergencyContactRequest;
use App\Http\Requests\EmergencyContact\FetchEmergencyContactRequest;
use App\Http\Requests\EmergencyContact\UpdateEmergencyContactRequest;
use App\Services\EmergencyContact\EmergencyContactService;
use Exception;

class EmergencyContactController extends Controller
{
    // Local variable
    protected $emergencyContactService;

    // Constructor
    public function __construct(EmergencyContactService $emergencyContactService)
    {
        // Inject service
        $this->emergencyContactService = $emergencyContactService;
    }

    // Create emergency contact
    public function create(CreateEmergencyContactRequest $request)
    {
        try {

            // Create emergency contact
            $emergencyContact = $this->emergencyContactService->createEmergencyContact($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_CREATED,
                'emergency-contact' => $emergencyContact,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch user's emergency contacts
    public function fetch(FetchEmergencyContactRequest $request)
    {
        try {

            // Fetch user's emergency contacts
            $emergencyContacts = $this->emergencyContactService->fetchEmergencyContacts($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'emergency-contacts' => $emergencyContacts,
            ]);
        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete emergency contact
    public function delete(DeleteEmergencyContactRequest $request)
    {
        try {

            // Delete emergency contact
            $this->emergencyContactService->deleteEmergencyContact($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'message' => ResponseMessage::deleteSuccess('Emergency Contact'),
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update emergency contact
    public function update(UpdateEmergencyContactRequest $request)
    {
        try {
            // Update emergency contact
            $emergencyContact = $this->emergencyContactService->updateEmergencyContact($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'emergency-contact' => $emergencyContact,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}