<?php
namespace App\Services\Qualification;

use App\Helpers\Response;
use App\Models\Qualification;
use App\Models\User;

class QualificationService
{
    // Create Qualification
    public function createQualification($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Cast $request->qualifications to $qualifications variable
        $qualifications = $request->qualifications;

        foreach ($qualifications as $qualification):

            // Initiate instance of Qualification
            $skill = new Qualification();
            $skill->user = $user->id;
            $skill->qualification = $qualification['skill'];
            $skill->save();

        endforeach;

        // Return success response
        return Response::success([
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

    // Update Qualification
    public function updateQualification($request)
    {
        // Get qualification
        $qualification = Qualification::findOrFail($request->qualification);

        // Update $qualification
        $qualification->qualification = $request->skill;
        $qualification->save();

        // Return success response
        return Response::success([
            'qualification' => $qualification,
        ]);

    }

    // Delete qualification
    public function deleteQualification($request)
    {
        // Get qualification
        $qualification = Qualification::findOrFail($request->qualification);

        // Delete $qualification
        $qualification->delete();

        // Return success response
        return Response::success([
            'qualification' => $qualification,
        ]);
    }

}