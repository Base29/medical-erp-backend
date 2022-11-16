<?php
namespace App\Services\Practice;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\Practice;
use App\Models\User;

class PracticeService
{
    // Create practice
    public function createPractice($request)
    {
        // Get user
        $practiceManager = User::findOrFail($request->practice_manager);

        // Create practice with the provided name
        $practice = new Practice();
        $practice->practice_name = $request->name;
        $practice->practice_manager = $practiceManager->id;
        $practice->save();

        return Response::success([
            'practice' => $practice->with('practiceManager')
                ->latest()
                ->first(),
        ]);
    }

    // Delete practice
    public function deletePractice($id)
    {
        // Check if practice exists
        $practice = Practice::findOrFail($id);

        if (!$practice) {
            throw new \Exception(ResponseMessage::notFound('Practice', $id, false));
        }

        // Deleting practice
        $practice->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Practice')]);
    }

    // Fetch practices
    public function fetchPractices()
    {
        if (request()->paginate === 'yes'):

            // Fetch practices
            $practices = Practice::with('policies', 'users.profile', 'practiceManager.profile')->latest()->paginate(10);
        else:
            // Fetch practices
            $practices = Practice::with('policies', 'users.profile', 'practiceManager.profile')->latest()->get();
        endif;

        return Response::success(['practices' => $practices]);
    }

    // Assign user to practice
    public function assignUserToPractice($request)
    {
        // Get User
        $user = User::where('email', $request->email)->firstOrFail();

        // Get Practice
        $practice = Practice::findOrFail($request->practice);

        // Checking if the user is already assigned to the provided practice
        $userAlreadyAssignedToPractice = $user->practices->contains('id', $practice->id);

        if ($userAlreadyAssignedToPractice) {
            throw new \Exception(ResponseMessage::alreadyAssigned($user->email, $practice->practice_name));
        }

        // // Check if $request has type === 'practice_manager
        // if ($request->type === 'practice-manager') {
        //     if ($practice->hasManager()) {
        //         throw new \Exception(ResponseMessage::customMessage('Practice ' . $practice->practice_name . ' already have a practice manager assigned to it'));
        //     }
        // }

        // Attach user to practice
        $user->practices()->attach($practice->id, [
            'type' => 'user',
        ]);

        return Response::success(['message' => ResponseMessage::assigned($user->email, $practice->practice_name)]);
    }

    // Revoke user from practice
    public function revokeUserFromPractice($request)
    {
        // Get User
        $user = User::where('email', $request->email)->firstOrFail();

        // Get Practice
        $practice = Practice::findOrFail($request->practice);

        // Check if the user is already assigned to the practice
        $associatedToPractice = $user->practices->contains('id', $practice->id);

        if (!$associatedToPractice) {
            throw new \Exception(ResponseMessage::notBelongTo($user->email, $practice->practice_name));
        }

        // Revoke user from practice
        $user->practices()->detach($practice->id);

        return Response::success(['message' => ResponseMessage::revoked($user->email, $practice->practice_name)]);
    }
}