<?php

namespace App\Http\Requests\InductionSchedule;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateInductionScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'induction_checklist_id' => 'required|numeric|exists:induction_checklists,id',
            'user_id' => 'required|numeric|exists:users,id',
            'date' => 'required|date|date_format:Y-m-d',
            'time' => 'required|date|date_format:H:i',
            'duration' => 'required|string',
            'is_hq_required' => 'required|boolean',
            'hq_staff_role_id' => 'required_if:is_hq_required,1|exists:roles,id',
            'hq_staff_id' => 'required_if:is_hq_required,1|exists:users,id',
            'is_additional_staff_required' => 'required|boolean',
            'additional_staff_role_id' => 'required_if:is_additional_staff_required,1|exists:roles,id',
            'additional_staff_id' => 'required_if:is_additional_staff_required,1|exists:users,id',
            'is_completed' => 'nullable|boolean',
            'completed_date' => 'required_if:is_completed,1|date|date_format:Y-m-d',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}