<?php
namespace App\Services\Locum;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\LocumInvoice;
use App\Models\LocumNote;
use App\Models\LocumSession;
use App\Models\LocumSessionInvite;
use App\Models\Practice;
use App\Models\Role;
use App\Models\User;
use App\Notifications\Locum\RemoveLocumFromSessionNotification;
use App\Notifications\Locum\SessionInvitationNotification;
use App\Notifications\Locum\SessionInviteAcceptedNotification;
use App\Notifications\Locum\SessionInviteDeclinedNotification;
use Exception;
use Illuminate\Support\Carbon;

class LocumService
{
    // Create locum session
    public function createLocumSession($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get role
        $role = Role::findOrFail($request->role);

        // Instance of LocumSession
        $locumSession = new LocumSession();
        $locumSession->practice_id = $practice->id;
        $locumSession->name = $request->name;
        $locumSession->start_date = $request->start_date;
        $locumSession->end_date = $request->end_date;
        $locumSession->start_time = $request->start_time;
        $locumSession->end_time = $request->end_time;
        $locumSession->rate = $request->rate;
        $locumSession->unit = $request->unit;
        $locumSession->location = $practice->practice_name;

        // Save $locumSession
        $role->locumSessions()->save($locumSession);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_CREATED,
            'locum-session' => $locumSession->with('practice', 'role')->latest()->first(),
        ]);
    }

    // Assign hired users to locum sessions
    public function addLocumToSession($request)
    {
        // Get locum session
        $locumSession = LocumSession::findOrFail($request->locum_session);

        // Get user
        $user = User::findOrFail($request->user);

        // Check if $user->is_active === true
        if (!$user->is_active) {
            throw new Exception(ResponseMessage::customMessage('User is not active.'), Response::HTTP_BAD_REQUEST);
        }

        // Check if the user is a candidate and is hired
        if (!$user->is_candidate || !$user->is_hired) {
            throw new Exception(ResponseMessage::customMessage('The user should be a candidate and should already be hired before adding to a locum session'), Response::HTTP_BAD_REQUEST);
        }

        // Check if the user is already assigned to the locum session
        if ($locumSession->userAlreadyAssignedToSession($user->id)) {
            throw new Exception(ResponseMessage::customMessage('User ' . $user->id . ' already assigned to locum session'), Response::HTTP_CONFLICT);
        }

        // Check to restrict if locums are being adding above the required quantity
        if ($locumSession->quantity === $locumSession->locums()->count()) {
            throw new Exception(ResponseMessage::customMessage('Cannot add user to locum session more than the required quantity'), Response::HTTP_BAD_REQUEST);
        }

        // Add user to a locum session
        $locumSession->locums()->attach($user->id);

        // Change $user->is_locum === true
        $user->is_locum = 1;
        $user->save();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'message' => ResponseMessage::assigned($user->email, $locumSession->name),
        ]);
    }

    // Remove user from locum session
    public function removeLocumFromSession($request)
    {
        // Get locum session
        $locumSession = LocumSession::findOrFail($request->locum_session);

        // Get user
        $user = User::findOrFail($request->user);

        // Check if the user is already assigned to the locum session
        if (!$locumSession->userAlreadyAssignedToSession($user->id)) {
            throw new Exception(ResponseMessage::customMessage('User ' . $user->id . ' not assigned to locum session'), Response::HTTP_BAD_REQUEST);
        }

        // Remove user from a locum session
        $locumSession->locums()->detach($user->id);

        // // Change $user->is_locum === true
        // $user->is_locum = 0;
        // $user->save();

        // Send notification to locum on removing from session
        $user->notify(new RemoveLocumFromSessionNotification(
            $user,
            $locumSession
        ));

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'message' => ResponseMessage::revoked($user->email, $locumSession->name),
        ]);
    }

    // Fetch All Sessions
    public function fetchAllSessions($request)
    {

        $locumSessionsQuery = LocumSession::query();

        if ($request->has('practice')) {
            // Get practice
            $practice = Practice::findOrFail($request->practice);

            $locumSessionsQuery = $locumSessionsQuery->where('practice_id', $practice->id);
        }

        if ($request->has('role')) {
            // Get role
            $role = Role::findOrFail($request->role);

            $locumSessionsQuery = $locumSessionsQuery->where('role_id', $role->id);
        }

        if ($request->has('start_date')) {
            // Start Date
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date);

            $locumSessionsQuery = $locumSessionsQuery->whereDate('start_date', $startDate);
        }

        if ($request->has('end_date')) {
            // End Date
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date);

            $locumSessionsQuery = $locumSessionsQuery->whereDate('end_date', $endDate);
        }

        if ($request->has('rate')) {
            // Parse rate
            $rate = $request->rate;

            $locumSessionsQuery = $locumSessionsQuery->where('rate', $rate);
        }

        if ($request->has('name')) {
            $name = $request->name;

            $locumSessionsQuery = $locumSessionsQuery->where('name', 'like', '%' . $name . '%');
        }

        if ($request->has('quantity')) {
            $quantity = $request->quantity;

            $locumSessionsQuery = $locumSessionsQuery->where('quantity', $quantity);

        }

        if ($request->has('unit')) {
            $unit = $request->unit;

            $$locumSessionsQuery = $locumSessionsQuery->where('unit', $unit);
        }

        $filteredLocumSessions = $locumSessionsQuery->with('practice', 'role', 'locums.profile', 'locums.roles', 'locums.locumNotes', 'locums.qualifications')
            ->withCount(['locums'])
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'locum-sessions' => $filteredLocumSessions,
        ]);
    }

    // Fetch single locum session
    public function fetchSingleLocumSession($request)
    {
        // Get locum session
        $locumSession = LocumSession::where('id', $request->locum_session)
            ->with('practice', 'role', 'locums.profile', 'locums.roles', 'locums.locumNotes', 'locums.qualifications')
            ->withCount(['locums'])
            ->latest()
            ->firstOrFail();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'locum-session' => $locumSession,
        ]);
    }

    // Delete locum session
    public function deleteLocumSession($request)
    {
        // Get locum session
        $locumSession = LocumSession::findOrFail($request->locum_session);

        // Delete locum session
        $locumSession->delete();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_BAD_REQUEST,
            'locum-session' => $locumSession,
        ]);
    }

    // Fetch sessions by month
    public function fetchSessionsByMonth($request)
    {

        // Cast $request->date to variable
        $date = $request->date;

        // Parsing $date with Carbon
        $parsedDate = Carbon::createFromFormat('Y-m', $date);

        // Build session by month query
        $sessionsByMonthQuery = LocumSession::query();

        // Check if $request has location
        if ($request->has('location')) {
            $location = Practice::findOrFail($request->location);

            $sessionsByMonthQuery = $sessionsByMonthQuery->where('practice_id', $location->id);
        }

        // Get session by month
        $sessionsByMonthFiltered = $sessionsByMonthQuery->whereMonth('start_date', '=', $parsedDate->format('m'))
            ->with(['locums.profile', 'locums.roles', 'locums.locumNotes', 'locums.qualifications', 'role'])
            ->withCount(['locums'])
            ->latest()
            ->get();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'sessions-by-month' => $sessionsByMonthFiltered,
        ]);

    }

    // Fetch sessions by day
    public function fetchSessionsByDay($request)
    {
        // Cast $request->date to variable
        $date = $request->date;

        // Parsing $date with Carbon
        $parsedDate = Carbon::createFromFormat('Y-m-d', $date);

        // Get sessions by the date
        $sessionsByDay = LocumSession::whereDate('start_date', '=', $parsedDate->format('Y-m-d'))
            ->with(['locums.profile', 'locums.roles', 'locums.locumNotes', 'locums.qualifications'])
            ->withCount(['locums'])
            ->latest()
            ->get();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'sessions-by-day' => $sessionsByDay,
        ]);

    }

    // Invite users to sessions
    public function inviteUsersToLocumSession($request)
    {
        // Get locum session
        $session = LocumSession::findOrFail($request->session);

        // Cast $request->locums array to variable
        $locum = User::findOrFail($request->locum);

        // Check if $user->is_active === true
        if (!$locum->is_active) {
            throw new Exception(ResponseMessage::customMessage('User is not active.'), Response::HTTP_BAD_REQUEST);
        }

        // Check if the user is a candidate and is hired
        if (!$locum->is_candidate || !$locum->is_hired) {
            throw new Exception(ResponseMessage::customMessage('The user should be a candidate and should already be hired before invited to a locum session'), Response::HTTP_BAD_REQUEST);
        }

        // Check if $locum is blacklisted
        if ($locum->is_blacklisted) {
            throw new Exception(ResponseMessage::customMessage('Blacklisted locums cannot be invited to a session'), Response::HTTP_FORBIDDEN);
        }

        // Check if the user is already assigned to the locum session
        if ($session->userAlreadyAssignedToSession($locum->id)) {
            throw new Exception(ResponseMessage::customMessage('User ' . $locum->id . ' already assigned to locum session'), Response::HTTP_CONFLICT);
        }

        // // Check to restrict if locums are being adding above the required quantity
        // if ($session->quantity === $session->locums()->count()) {
        //     throw new Exception(ResponseMessage::customMessage('Cannot invite users to locum session more than the required quantity'));
        // }

        // // Check if user is a locum
        // if (!$locum->isLocum()) {
        //     throw new Exception(ResponseMessage::customMessage('Only users that are locums can be invited to a locum session'));
        // }

        // Instance of LocumSessionInvite model
        $locumSessionInvite = new LocumSessionInvite();

        if ($locumSessionInvite->alreadyInvitedForSession($session->id, $locum->id)) {
            throw new Exception(ResponseMessage::customMessage('Invite already sent.'), Response::HTTP_CONFLICT);
        }

        $locumSessionInvite->notifiable = auth()->user()->id;
        $locumSessionInvite->session = $session->id;
        $locumSessionInvite->locum = $locum->id;
        $locumSessionInvite->title = $session->name;
        $locumSessionInvite->save();

        // Sending notification to invited users
        $locum->notify(new SessionInvitationNotification(
            $locum,
            $session,
            $locumSessionInvite

        ));

        // Return success
        return Response::success([
            'code' => Response::HTTP_OK,
            'session' => $session->where('id', $session->id)->with('sessionInvites')->first(),
        ]);
    }

    // Accept locum session invitation
    public function sessionInvitationAction($request)
    {
        // Get session invitation
        $sessionInvite = LocumSessionInvite::where('id', $request->session_invite)->firstOrFail();

        // Get locum session
        $locumSession = LocumSession::findOrFail($sessionInvite->session);

        // Get user
        $user = auth()->user();

        // Get the creator of the invitation for notifying regarding action taken by the user
        $notifiable = User::findOrFail($sessionInvite->notifiable);

        // Check if the user is already assigned to the locum session
        if ($locumSession->userAlreadyAssignedToSession($user->id)) {
            throw new Exception(ResponseMessage::customMessage('User ' . $user->id . ' already assigned to locum session'), Response::HTTP_BAD_REQUEST);
        }

        // Casting $request->action to variable
        $action = $request->action;

        // Switch statement
        switch ($action) {
            // When a locum accepts invitation for a session
            case 2:

                // Check to restrict if locums are being adding above the required quantity
                if ($locumSession->locums()->count() === 1) {
                    throw new Exception(ResponseMessage::customMessage('Sorry seat for this session has been filled.'), Response::HTTP_CONFLICT);
                }

                // Check if the user already accepted the invitation
                if ($sessionInvite->status === 2) {
                    throw new Exception(ResponseMessage::customMessage('You have already accepted this invitation'), Response::HTTP_CONFLICT);
                }

                // Add user to a locum session
                $locumSession->locums()->attach($user->id);

                // Updated status of the invite
                $sessionInvite->status = 2;
                $sessionInvite->save();

                // Update user's is_locum status to true (1)
                $user->is_locum = 1;
                $user->save();

                // Add session to user's locum invoices
                $sessionInvoice = new LocumInvoice();
                $sessionInvoice->session = $locumSession->id;
                $sessionInvoice->locum = $user->id;
                $sessionInvoice->location = $locumSession->practice_id;
                $sessionInvoice->start_date = $locumSession->start_date;
                $sessionInvoice->end_date = $locumSession->end_date;
                $sessionInvoice->start_time = $locumSession->start_time;
                $sessionInvoice->end_time = $locumSession->end_time;
                $sessionInvoice->rate = $locumSession->rate;
                $sessionInvoice->save();

                $notifiable->notify(new SessionInviteAcceptedNotification(
                    $user,
                    $locumSession,
                    $sessionInvite,
                    $notifiable
                ));
                break;

            // When a locum declines invitation for a session
            case 3:

                // Check if the user already declined the invitation
                if ($sessionInvite->status === 3) {
                    throw new Exception(ResponseMessage::customMessage('You have already declined this invitation'), Response::HTTP_CONFLICT);
                }

                // Updated status of the invite
                $sessionInvite->status = 3;
                $sessionInvite->save();

                $notifiable->notify(new SessionInviteDeclinedNotification(
                    $user,
                    $locumSession,
                    $sessionInvite,
                    $notifiable
                ));

                break;

            default:
                return false;
        }

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'session-invite' => $sessionInvite->where(['id' => $sessionInvite->id, 'locum' => $user->id])
                ->with('session')
                ->first(),
        ]);
    }

    public function uploadSessionInvoice($request)
    {
        // Get locum invoice
        $sessionInvoice = LocumInvoice::where('session', $request->session)->firstOrFail();

        // Path on S3
        $folderPath = 'locum/user-' . $sessionInvoice->locum . '/session-' . $sessionInvoice->session . '/invoice';

        // Upload invoice
        $invoiceUrl = FileUploadService::upload($request->invoice, $folderPath, 's3');

        // Save invoice url
        $sessionInvoice->session_invoice = $invoiceUrl;
        $sessionInvoice->save();

        // Return response
        return Response::success([
            'session-invoice' => $sessionInvoice,
        ]);

    }

    // Fetch user invoices
    public function fetchUserInvoices($request)
    {
        // Get authenticated user
        $authenticatedUser = auth()->user();

        // Locum invoice query
        $invoiceQuery = LocumInvoice::query();

        // $request has location
        if ($request->has('location')) {
            // Get practice
            $practice = Practice::findOrFail($request->location);

            $invoiceQuery = $invoiceQuery->where('location', $practice->id);
        }

        // If request has role
        if ($request->has('role')) {
            // Get role
            $role = Role::findOrFail($request->role);

            $invoiceQuery = $invoiceQuery->whereHas('session', function ($q) use ($role) {
                $q->where('role_id', $role->id);
            });
        }

        // if $request has start_date
        if ($request->has('start_date')) {
            // Start Date
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date);

            $invoiceQuery = $invoiceQuery->whereDate('start_date', $startDate);
        }

        // If $request has end_date
        if ($request->has('end_date')) {
            // End Date
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date);

            $invoiceQuery = $invoiceQuery->whereDate('end_date', $endDate);
        }

        // If $request has rate
        if ($request->has('rate')) {
            // Parse rate
            $rate = $request->rate;

            $invoiceQuery = $invoiceQuery->where('rate', $rate);
        }

        // If request has esm_status
        if ($request->has('esm_status')) {
            $invoiceQuery = $invoiceQuery->where('esm_status', $request->esm_status);
        }

        // If $request has invoice_status
        //TODO: This filter hasn't been clarified in the user stories. Once it is discussed it will be added

        // Get locum invoices
        $filteredInvoices = $invoiceQuery->where('locum', $authenticatedUser->id)
            ->with(['locum.profile', 'session', 'location'])
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'locum-invoices' => $filteredInvoices,
        ]);
    }

    // Fetch locum billing as a recruiter
    public function fetchAllLocumBilling($request)
    {
        // Locum invoice query
        $invoiceQuery = LocumInvoice::query();

        // $request has location
        if ($request->has('location')) {
            // Get practice
            $practice = Practice::findOrFail($request->location);

            $invoiceQuery = $invoiceQuery->where('location', $practice->id);
        }

        // If request has role
        if ($request->has('role')) {
            // Get role
            $role = Role::findOrFail($request->role);

            $invoiceQuery = $invoiceQuery->whereHas('session', function ($q) use ($role) {
                $q->where('role_id', $role->id);
            });
        }

        // if $request has start_date
        if ($request->has('start_date')) {
            // Start Date
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date);

            $invoiceQuery = $invoiceQuery->whereDate('start_date', $startDate);
        }

        // If $request has end_date
        if ($request->has('end_date')) {
            // End Date
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date);

            $invoiceQuery = $invoiceQuery->whereDate('end_date', $endDate);
        }

        // If $request has rate
        if ($request->has('rate')) {
            // Parse rate
            $rate = $request->rate;

            $invoiceQuery = $invoiceQuery->where('rate', $rate);
        }

        // If request has esm_status
        if ($request->has('esm_status')) {
            $invoiceQuery = $invoiceQuery->where('esm_status', $request->esm_status);
        }

        // If $request has invoice_status
        //TODO: This filter hasn't been clarified in the user stories. Once it is discussed it will be added

        // Get locum invoices
        $filteredInvoices = $invoiceQuery->with(['locum.profile', 'session', 'location'])
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'locum-invoices' => $filteredInvoices,
        ]);
    }

    // Update ESM Status of Locum Invoice
    public function updateEsmStatus($request)
    {
        // Get locum invoice
        $locumInvoice = LocumInvoice::findOrFail($request->invoice);

        // Update esm_status
        $locumInvoice->esm_status = $request->esm_status;
        $locumInvoice->save();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'locum-invoice' => $locumInvoice,
        ]);
    }

    // Blacklist Locum
    public function addLocumToBlacklist($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Check if user is locum
        if (!$user->isLocum()) {
            throw new Exception(ResponseMessage::customMessage('User should be a locum'), Response::HTTP_CONFLICT);
        }

        if ($user->is_blacklisted === 1) {
            throw new Exception(ResponseMessage::customMessage('User is already blacklisted'), Response::HTTP_FORBIDDEN);
        }

        // Blacklist user
        $user->is_blacklisted = 1;
        $user->blacklist_reason = $request->blacklist_reason;
        $user->save();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'user' => $user->where('id', $user->id)
                ->with([
                    'profile.applicant',
                    'positionSummary',
                    'contractSummary',
                    'roles',
                    'practices',
                    'employmentCheck',
                    'workPatterns.workTimings',
                    'courses.modules.lessons',
                    'locumNotes',
                    'qualifications',
                ])
                ->first(),
        ]);
    }

    // Remove from blacklist
    public function removeLocumFromBlacklist($request)
    {
        // Get user
        $user = User::findOrFail($request->locum);

        if (!$user->is_blacklisted) {
            throw new Exception(ResponseMessage::customMessage('User is not blaclisted'), Response::HTTP_FORBIDDEN);
        }

        // Blacklist user
        $user->is_blacklisted = 0;
        $user->blacklist_reason = '';
        $user->save();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'user' => $user->where('id', $user->id)
                ->with([
                    'profile.applicant',
                    'positionSummary',
                    'contractSummary',
                    'roles',
                    'practices',
                    'employmentCheck',
                    'workPatterns.workTimings',
                    'courses.modules.lessons',
                    'locumNotes',
                    'qualifications',
                ])
                ->first(),
        ]);
    }

    // Create note for locum (formerly known as privileges)
    public function createLocumNote($request)
    {
        // Get locum
        $locum = User::findOrFail($request->locum);

        // Check if $locum->is_locum = true
        if (!$locum->is_locum) {
            throw new Exception(ResponseMessage::customMessage('User must be a locum'), Response::HTTP_CONFLICT);
        }

        // Cast $request->notes to array
        $notes = $request->notes;

        foreach ($notes as $note):

            // Initiate instance of LocumNote
            $locumNote = new LocumNote();
            $locumNote->locum = $locum->id;
            $locumNote->note = $note['note'];
            $locumNote->save();

        endforeach;

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'locum' => $locum->where('id', $locum->id)
                ->with([
                    'profile.applicant',
                    'positionSummary',
                    'contractSummary',
                    'roles',
                    'practices',
                    'employmentCheck',
                    'workPatterns.workTimings',
                    'courses.modules.lessons',
                    'locumNotes',
                    'qualifications',
                ])
                ->first(),
        ]);
    }

    // Update locum note
    public function updateLocumNote($request)
    {

        // Get locum note
        $locumNote = LocumNote::findOrFail($request->locum_note);

        // Update note
        $locumNote->note = $request->note;
        $locumNote->update();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'locum-note' => $locumNote,
        ]);
    }

    // Delete locum note
    public function deleteLocumNote($request)
    {
        // Get locum note
        $locumNote = LocumNote::findOrFail($request->locum_note);

        // Delete locum note
        $locumNote->delete();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'locum-note' => $locumNote,
        ]);
    }
}