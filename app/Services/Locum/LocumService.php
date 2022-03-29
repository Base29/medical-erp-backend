<?php
namespace App\Services\Locum;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\LocumSession;
use App\Models\Practice;
use App\Models\Role;
use App\Models\User;

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
        if ($locumSession->quantity === $locumSession->users()->count()) {
            throw new \Exception(ResponseMessage::customMessage('Cannot add user to locum session more than the required quantity'));
        }

        // Add user to a locum session
        $locumSession->users()->attach($user->id);

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
}