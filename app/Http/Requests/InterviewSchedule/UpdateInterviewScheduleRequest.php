<?php

namespace App\Http\Requests\InterviewSchedule;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateInterviewScheduleRequest extends FormRequest
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
            'interview' => 'required|numeric|exists:interview_schedules,id',
            'is_completed' => 'nullable|boolean',
            'progress' => 'nullable|numeric',
            'applicant_status' => [
                'required',
                Rule::in([
                    0, // Rejected
                    1, // Accepted
                    2, // Referred for 2nd Interview
                ]),
            ],
        ];
    }

    public function messages()
    {
        return [
            'applicant_status.in' => 'The applicant_status can be 0 => Rejected | 1 => Accepted | 2 => Referred for 2nd Interview',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}