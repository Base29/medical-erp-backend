<?php

namespace App\Http\Requests\EmploymentHistory;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateEmploymentHistoryRequest extends FormRequest
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
            'employment_history' => 'required|numeric|exists:employment_histories,id',
            'employer_name' => 'string|max:50',
            'address' => 'string',
            'phone_number' => 'string',
            'type_of_business' => 'string',
            'job_title' => 'string',
            'job_start_date' => 'date|date_format:Y-m-d',
            'job_end_date' => 'date|date_format:Y-m-d',
            'salary' => 'string',
            'reporting_to' => 'string',
            'period_of_notice' => 'string',
            'can_contact_referee' => 'boolean',
            'reason_for_leaving' => 'string|max:500',
            'responsibilities_duties_desc' => 'string|max:500',
            'is_current' => 'boolean',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}