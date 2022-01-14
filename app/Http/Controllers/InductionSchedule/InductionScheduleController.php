<?php

namespace App\Http\Controllers\InductionSchedule;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\InductionSchedule\CreateInductionScheduleRequest;
use App\Models\InductionChecklist;
use App\Models\InductionSchedule;
use App\Models\User;

class InductionScheduleController extends Controller
{
    // Create induction schedule
    public function create(CreateInductionScheduleRequest $request)
    {
        try {

            // Get user
            $user = User::findOrFail($request->user_id);

            // Get induction checklist
            $inductionChecklist = InductionChecklist::findOrFail($request->induction_checklist);

            // Instance of InductionSchedule model
            $inductionSchedule = new InductionSchedule();
            $inductionSchedule->induction_checklist_id = $inductionChecklist->id;
            $inductionSchedule->schedule_date = $request->schedule_date;
            $inductionSchedule->schedule_time = $request->schedule_time;
            $inductionSchedule->schedule_duration = $request->schedule_duration;
            $inductionSchedule->is_hq_required = $request->is_hq_required;
            $inductionSchedule->hq_staff_role_id = $request->hq_staff_role_id;
            $inductionSchedule->hq_staff_id = $request->hq_staff_id;
            $inductionSchedule->is_additional_staff_required = $request->is_additional_staff_required;
            $inductionSchedule->additional_staff_role_id = $request->additional_staff_role_id;
            $inductionSchedule->additional_staff_id = $request->additional_staff_id;

            // Save induction Schedule
            $user->inductionSchedule()->save($inductionSchedule);

            // Return success response
            return Response::success([
                'induction-schedule' => $inductionSchedule->with('user', 'inductionChecklist'),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}