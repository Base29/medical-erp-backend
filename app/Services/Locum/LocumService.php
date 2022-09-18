<?php
namespace App\Services\Locum;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\LocumSession;
use App\Models\LocumSessionInvite;
use App\Models\Practice;
use App\Models\Role;
use App\Models\User;
use App\Notifications\Locum\SessionInvitationNotification;
use App\Notifications\Locum\SessionInviteAcceptedNotification;
use App\Notifications\Locum\SessionInviteDeclinedNotification;
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
        $locumSession->quantity = $request->quantity;
        $locumSession->start_date = $request->start_date;
        $locumSession->end_date = $request->end_date;
        $locumSession->start_time = $request->start_time;
        $locumSession->end_time = $request->end_time;
        $locumSession->rate = $request->rate;
        $locumSession->unit = $request->unit;
        $locumSession->location = $request->location;

        // Save $locumSession
        $role->locumSessions()->save($locumSession);

        // Return success response
        return Response::success([
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
            throw new \Exception(ResponseMessage::customMessage('User is not active.'));
        }

        // Check if the user is a candidate and is hired
        if (!$user->is_candidate || !$user->is_hired) {
            throw new \Exception(ResponseMessage::customMessage('The user should be a candidate and should already be hired before adding to a locum session'));
        }

        // Check if the user is already assigned to the locum session
        if ($locumSession->userAlreadyAssignedToSession($user->id)) {
            throw new \Exception(ResponseMessage::customMessage('User ' . $user->id . ' already assigned to locum session'));
        }

        // Check to restrict if locums are being adding above the required quantity
        if ($locumSession->quantity === $locumSession->locums()->count()) {
            throw new \Exception(ResponseMessage::customMessage('Cannot add user to locum session more than the required quantity'));
        }

        // Add user to a locum session
        $locumSession->locums()->attach($user->id);

        // Change $user->is_locum === true
        $user->is_locum = 1;
        $user->save();

        // Return success response
        return Response::success(['message' => ResponseMessage::assigned($user->email, $locumSession->name)]);
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
            throw new \Exception(ResponseMessage::customMessage('User ' . $user->id . ' not assigned to locum session'));
        }

        // Remove user from a locum session
        $locumSession->users()->detach($user->id);

        // Change $user->is_locum === true
        $user->is_locum = 0;
        $user->save();

        // Return success response
        return Response::success(['message' => ResponseMessage::revoked($user->email, $locumSession->name)]);
    }

    // Fetch All Sessions
    public function fetchAllSessions($request)
    {
        // // If $request->practice is available
        // if ($request->has('practice')) {

        //     // Get practice
        //     $practice = Practice::findOrFail($request->practice);

        //     // Get sessions
        //     $locumSessions = LocumSession::where('practice_id', $practice->id)
        //         ->with('practice', 'role', 'users.profile')
        //         ->latest()
        //         ->paginate(10);

        // } else {

        //     // Get sessions
        //     $locumSessions = LocumSession::with('practice', 'role', 'users.profile')
        //         ->latest()
        //         ->paginate(10);
        // }

        $filters = [];

        if ($request->has('practice')) {
            // Get practice
            // $practice = Practice::findOrFail($request->practice);

            $filters['practice'] = $request->practice;
        }

        if ($request->has('role')) {
            // Get role
            // $role = Role::findOrFail($request->role);

            $filters['role'] = $request->role;
        }

        if ($request->has('start_date')) {
            // Start Date
            // $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date);

            $filters['start_date'] = $request->start_date;
        }

        if ($request->has('end_date')) {
            // End Date
            // $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date);

            $filters['end_date'] = $request->end_date;
        }

        if ($request->has('rate')) {
            // Parse rate
            $rate = $request->rate;

            $filters['rate'] = $rate;
        }

        if ($request->has('name')) {
            $name = $request->name;

            $filters['name'] = $name;
        }

        if ($request->has('quantity')) {
            $quantity = $request->quantity;

            $filters['quantity'] = $quantity;
        }

        if ($request->has('unit')) {
            $unit = $request->unit;

            $filters['unit'] = $unit;
        }

        $locumSessions = LocumSession::where(function ($q) use ($filters) {
            $q->where('practice_id', $filters['practice'])
                ->orWhere('name', 'like', '%' . $filters['name'] . '%')
                ->orWhere('role_id', $filters['role'])
                ->orWhereDate('start_date', $filters['start_date'])
                ->orWhereDate('end_date', $filters['end_date'])
                ->orWhere('quantity', $filters['quantity'])
                ->orWhere('unit', $filters['unit'])
                ->orWhere('rate', $filters['rate']);
        })
            ->with('practice', 'role', 'locums.profile')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'locum-sessions' => $locumSessions,
        ]);
    }

    // Fetch single locum session
    public function fetchSingleLocumSession($request)
    {
        // Get locum session
        $locumSession = LocumSession::where('id', $request->locum_session)
            ->with('practice', 'role', 'users.profile')
            ->latest()
            ->firstOrFail();

        // Return success response
        return Response::success([
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
            'message' => ResponseMessage::deleteSuccess('Locum Session ' . $locumSession->id),
        ]);
    }

    // Fetch sessions by month
    public function fetchSessionsByMonth($request)
    {

        // Cast $request->date to variable
        $date = $request->date;

        // Parsing $date with Carbon
        $parsedDate = Carbon::createFromFormat('Y-m', $date);

        // Get session by month
        $sessionsByMonth = LocumSession::whereMonth('start_date', '=', $parsedDate->format('m'))
            ->with(['locums.profile'])
            ->latest()
            ->get();

        // Return success response
        return Response::success([
            'sessions-by-month' => $sessionsByMonth,
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
            ->with(['locums.profile'])
            ->latest()
            ->get();

        // Return success response
        return Response::success([
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
            throw new \Exception(ResponseMessage::customMessage('User is not active.'));
        }

        // Check if the user is a candidate and is hired
        if (!$locum->is_candidate || !$locum->is_hired) {
            throw new \Exception(ResponseMessage::customMessage('The user should be a candidate and should already be hired before invited to a locum session'));
        }

        // Check if the user is already assigned to the locum session
        if ($session->userAlreadyAssignedToSession($locum->id)) {
            throw new \Exception(ResponseMessage::customMessage('User ' . $locum->id . ' already assigned to locum session'));
        }

        // // Check to restrict if locums are being adding above the required quantity
        // if ($session->quantity === $session->locums()->count()) {
        //     throw new \Exception(ResponseMessage::customMessage('Cannot invite users to locum session more than the required quantity'));
        // }

        // Check if user is a locum
        if (!$locum->isLocum()) {
            throw new \Exception(ResponseMessage::customMessage('Only users that are locums can be invited to a locum session'));
        }

        // Instance of LocumSessionInvite model
        $locumSessionInvite = new LocumSessionInvite();

        if ($locumSessionInvite->alreadyInvitedForSession($session->id, $locum->id)) {
            throw new \Exception(ResponseMessage::customMessage('Invite already sent.'));
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
            throw new \Exception(ResponseMessage::customMessage('User ' . $user->id . ' already assigned to locum session'));
        }

        // Casting $request->action to variable
        $action = $request->action;

        // Switch statement
        switch ($action) {
            // When a locum accepts invitation for a session
            case 2:

                // Check to restrict if locums are being adding above the required quantity
                if ($locumSession->quantity === $locumSession->locums()->count()) {
                    throw new \Exception(ResponseMessage::customMessage('Sorry all the seats are filled within this session'));
                }

                // Check if the user already accepted the invitation
                if ($sessionInvite->status === 2) {
                    throw new \Exception(ResponseMessage::customMessage('You have already accepted this invitation'));
                }

                // Add user to a locum session
                $locumSession->locums()->attach($user->id);

                // Updated status of the invite
                $sessionInvite->status = 2;
                $sessionInvite->save();

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
                    throw new \Exception(ResponseMessage::customMessage('You have already declined this invitation'));
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
            'session-invite' => $sessionInvite->where(['id' => $sessionInvite->id, 'locum' => $user->id])
                ->with('session')
                ->first(),
        ]);
    }
}