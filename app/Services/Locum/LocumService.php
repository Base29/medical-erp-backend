<?php
namespace App\Services\Locum;

use App\Helpers\Response;
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

        // Check to red
    }
}