<?php

namespace App\Http\Requests\InductionSchedule;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateInductionScheduleRequest extends FormRequest
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
            'induction_schedule' => 'required|numeric|exists:induction_schedules,id',
            'date' => 'nullable|date|date_format:Y-m-d',
            'time' => 'nullable|date_format:H:i',
            'duration' => 'nullable|string',
            'is_hq_required' => 'nullable|boolean',
            'hq_staff_role_id' => 'required_if:is_hq_required,1|exists:roles,id',
            'hq_staff_id' => 'required_if:is_hq_required,1|exists:users,id',
            'is_additional_staff_required' => 'nullable|boolean',
            'additional_staff_role_id' => 'required_if:is_additional_staff_required,1|exists:roles,id',
            'additional_staff_id' => 'required_if:is_additional_staff_required,1|exists:users,id',
            'checklists' => 'nullable|array',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}