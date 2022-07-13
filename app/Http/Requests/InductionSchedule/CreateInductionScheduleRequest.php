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
            'practice' => 'required|numeric|exists:practices,id',
            'user' => 'required|numeric|exists:users,id',
            'date' => 'required|date|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'duration' => 'required|string',
            'is_hq_required' => 'nullable|boolean',
            'hq_staff_role_id' => 'nullable|exists:roles,id',
            'hq_staff_id' => 'nullable|exists:users,id',
            'is_additional_staff_required' => 'nullable|boolean',
            'additional_staff_role_id' => 'nullable|exists:roles,id',
            'additional_staff_id' => 'nullable|exists:users,id',
            'checklists' => 'required|array',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}