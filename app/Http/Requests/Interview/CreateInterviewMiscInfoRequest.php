<?php

namespace App\Http\Requests\Interview;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateInterviewMiscInfoRequest extends FormRequest
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
            'current_salary' => 'required|string|max:20',
            'expected_salary' => 'required|string|max:20',
            'difference' => 'required|string|max:20',
            'availability' => 'required|string|max:20',
            'available_time' => 'required|string|max:20',
            'job_type' => 'required|string|max:20',
            'dbs' => 'required|string|max:20',
            'dismissals' => 'required|boolean',
            'given_notice' => 'required|boolean',
            'notice_start' => 'required|date|date_format:Y-m-d',
            'notice_duration' => 'required|string|max:20',
            'interviewing_elsewhere' => 'required|boolean',
            'salary_notes' => 'nullable|string|max:1000',
            'notice_notes' => 'nullable|string|max:1000',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}