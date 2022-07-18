<?php
namespace App\Services\InductionSchedule;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\InductionSchedule;
use App\Models\Practice;
use App\Models\User;

class InductionScheduleService
{
    // Create Induction schedule
    public function createInductionSchedule($request)
    {
        // Get Practice
        $practice = Practice::findOrFail($request->practice);

        // Get user
        $user = User::findOrFail($request->user);

        // Instance of InductionSchedule model
        $inductionSchedule = new InductionSchedule();
        $inductionSchedule->practice_id = $practice->id;
        $inductionSchedule->date = $request->date;
        $inductionSchedule->time = $request->time;
        $inductionSchedule->duration = $request->duration;
        $inductionSchedule->is_hq_required = $request->is_hq_required;
        $inductionSchedule->hq_staff_role_id = $request->is_hq_required ? $request->hq_staff_role_id : null;
        $inductionSchedule->hq_staff_id = $request->is_hq_required ? $request->hq_staff_id : null;
        $inductionSchedule->is_additional_staff_required = $request->is_additional_staff_required;
        $inductionSchedule->additional_staff_role_id = $request->is_additional_staff_required ? $request->additional_staff_role_id : null;
        $inductionSchedule->additional_staff_id = $request->is_additional_staff_required ? $request->additional_staff_id : null;

        // Check if the $user already has a schedule
        if ($user->inductionAlreadyScheduled()) {
            throw new \Exception(ResponseMessage::customMessage('User already has a schedule'));
        }

        // Save induction Schedule
        $user->inductionSchedule()->save($inductionSchedule);

        // Save checklists related to induction schedule
        $inductionSchedule->inductionChecklists()->sync($this->mapChecklists($request->checklists));

        // Return success response
        return $inductionSchedule
            ->with('user', 'practice', 'inductionChecklists.inductionQuestions')
            ->first();
    }

    // Helper function
    private function mapChecklists($checklists)
    {
        return collect($checklists)->map(function ($checklist) {
            return [
                'induction_checklist_id' => $checklist['induction_checklist_id'],
                'is_complete' => $checklist['is_complete'],
                'completed_date' => $checklist['completed_date'],
            ];
        });
    }

    // Fetch induction schedule
    public function fetchInductionSchedules($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get induction schedules for the $practice
        return InductionSchedule::where('practice_id', $practice->id)
            ->with('user', 'practice', 'inductionChecklists.inductionQuestions')
            ->latest()
            ->paginate(10);
    }

    // Delete induction schedule
    public function deleteInductionSchedule($request)
    {
        // Get induction schedule
        $inductionSchedule = InductionSchedule::findOrFail($request->induction_schedule);

        // Delete induction schedule
        $inductionSchedule->delete();
    }

    // Fetch user induction
    public function fetchUserInduction($request)
    {
        // Get user induction
        $userInduction = InductionSchedule::where('user_id', $request->user)
            ->with('inductionChecklists.inductionQuestions')
            ->firstOrFail();

        // Return success response
        return Response::success([
            'user-induction' => $userInduction,
        ]);
    }

    // Fetch single induction
    public function fetchSingleInduction($request)
    {
        // Get induction
        $induction = InductionSchedule::where('id', $request->induction)
            ->with('inductionChecklists.inductionQuestions')
            ->firstOrFail();

        // Return success response
        return Response::success([
            'induction' => $induction,
        ]);
    }

    // Update Induction schedule
    public function updateInductionSchedule($request)
    {
        // Get induction schedule
        $inductionSchedule = InductionSchedule::findOrFail($request->induction);

        UpdateService::updateModel($inductionSchedule, $request->validated(), 'induction');

        // Return success response
        return Response::success([
            'interview' => $inductionSchedule->with('practice', 'user.profile')
                ->latest('updated_at')
                ->first(),
        ]);
    }
}