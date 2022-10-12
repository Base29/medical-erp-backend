<?php

namespace App\Http\Controllers\Locum;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Locum\AddLocumToBlacklistRequest;
use App\Http\Requests\Locum\AssignUserToLocumSessionRequest;
use App\Http\Requests\Locum\CreateLocumNoteRequest;
use App\Http\Requests\Locum\CreateLocumSessionRequest;
use App\Http\Requests\Locum\DeleteLocumNoteRequest;
use App\Http\Requests\Locum\DeleteLocumSessionRequest;
use App\Http\Requests\Locum\FetchLocumSessionsRequest;
use App\Http\Requests\Locum\FetchSessionsByDayRequest;
use App\Http\Requests\Locum\FetchSessionsByMonthRequest;
use App\Http\Requests\Locum\FetchSingleLocumSessionRequest;
use App\Http\Requests\Locum\FilterLocumInvoicesRequest;
use App\Http\Requests\Locum\InviteUsersToLocumSessionRequest;
use App\Http\Requests\Locum\RemoveLocumFromBlacklistRequest;
use App\Http\Requests\Locum\RemoveUserFromLocumSessionRequest;
use App\Http\Requests\Locum\SessionInvitationActionRequest;
use App\Http\Requests\Locum\UpdateEsmStatusRequest;
use App\Http\Requests\Locum\UpdateLocumNoteRequest;
use App\Http\Requests\Locum\UploadSessionInvoiceRequest;
use App\Services\Locum\LocumService;

class LocumController extends Controller
{
    // Local variable
    protected $locumService;

    // Constructor
    public function __construct(LocumService $locumService)
    {
        // Inject Service
        $this->locumService = $locumService;
    }

    // Create session
    public function create(CreateLocumSessionRequest $request)
    {
        try {

            // Create locum session service
            return $this->locumService->createLocumSession($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Attach user to session
    public function assignUser(AssignUserToLocumSessionRequest $request)
    {
        try {

            // Assign user to session service
            return $this->locumService->addLocumToSession($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Remove user from session
    public function removeUser(RemoveUserFromLocumSessionRequest $request)
    {
        try {
            // Remove user from session service
            return $this->locumService->removeLocumFromSession($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch all locum sessions
    public function fetch(FetchLocumSessionsRequest $request)
    {
        try {
            // Fetch all locum session service
            return $this->locumService->fetchAllSessions($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single locum session
    public function fetchSingle(FetchSingleLocumSessionRequest $request)
    {
        try {
            // Fetch single locum session service
            return $this->locumService->fetchSingleLocumSession($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function delete(DeleteLocumSessionRequest $request)
    {
        try {
            // Delete locum session service
            return $this->locumService->deleteLocumSession($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch by month
    public function fetchByMonth(FetchSessionsByMonthRequest $request)
    {
        try {
            // Logic here
            return $this->locumService->fetchSessionsByMonth($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch by day
    public function fetchByDay(FetchSessionsByDayRequest $request)
    {
        try {
            // Logic here
            return $this->locumService->fetchSessionsByDay($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Invite users to session
    public function inviteUsersToSession(InviteUsersToLocumSessionRequest $request)
    {
        try {
            // Logic here
            return $this->locumService->inviteUsersToLocumSession($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Accept session invitation
    public function invitationAction(SessionInvitationActionRequest $request)
    {
        try {
            // Logic here
            return $this->locumService->sessionInvitationAction($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Upload invoice
    public function uploadInvoice(UploadSessionInvoiceRequest $request)
    {
        try {
            // Logic here
            return $this->locumService->uploadSessionInvoice($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch invoices for authenticated user
    public function fetchInvoices(FilterLocumInvoicesRequest $request)
    {
        try {
            // Logic here
            return $this->locumService->fetchUserInvoices($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update esm status
    public function esmStatus(UpdateEsmStatusRequest $request)
    {
        try {
            // Logic here
            return $this->locumService->updateEsmStatus($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch locum session invoices as recruiter
    public function fetchLocumInvoices(FilterLocumInvoicesRequest $request)
    {
        try {
            // Logic here
            return $this->locumService->fetchAllLocumBilling($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Blacklist locum
    public function addToBlacklist(AddLocumToBlacklistRequest $request)
    {
        try {
            // Logic here
            return $this->locumService->addLocumToBlacklist($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Remove from blacklist
    public function removeFromBlacklist(RemoveLocumFromBlacklistRequest $request)
    {
        try {
            // Logic here
            return $this->locumService->removeLocumFromBlacklist($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Create note for locum
    public function createNote(CreateLocumNoteRequest $request)
    {
        try {
            // Logic here
            return $this->locumService->createLocumNote($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update note for locum
    public function updateNote(UpdateLocumNoteRequest $request)
    {
        try {
            // Logic here
            return $this->locumService->updateLocumNote($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete locum note
    public function deleteNote(DeleteLocumNoteRequest $request)
    {
        try {
            // Logic here
            return $this->locumService->deleteLocumNote($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}