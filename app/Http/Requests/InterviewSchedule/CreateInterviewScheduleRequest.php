<?php

namespace App\Http\Requests\InterviewSchedule;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateInterviewScheduleRequest extends FormRequest
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
            'interview_policy' => 'required|numeric|exists:interview_policies,id',
            'practice' => 'required|numeric|exists:practices,id',
            'hiring_request' => 'required|numeric|exists:hiring_requests,id',
            'user' => 'required|numeric|exists:users,id',
            'department' => 'required|numeric|exists:departments,id',
            'date' => 'required|date|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'location' => 'nullable|string|max:500',
            'interview_type' => [
                'required',
                Rule::in(['physical-interview', 'digital-interview']),
            ],
            'application_status' => [
                'required',
                Rule::in(['first-interview', 'second-interview', 'final-interview']),
            ],
        ];
    }

    public function messages()
    {
        return [
            'application_status.in' => 'The application_status is invalid. It should be one of first-interview|second-interview|final-interview',
            'interview_type.in' => 'The interview_type is invalid. It should be one of physical-interview|digital-interview',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}