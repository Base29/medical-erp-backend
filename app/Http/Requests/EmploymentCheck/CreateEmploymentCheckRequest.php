<?php

namespace App\Http\Requests\EmploymentCheck;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateEmploymentCheckRequest extends FormRequest
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
            'user' => 'required|numeric|exists:users,id',
            'passport_number' => 'nullable|string',
            'passport_country_of_issue' => 'nullable|string',
            'passport_date_of_expiry' => 'nullable|date|date_format:Y-m-d',
            'is_uk_citizen' => 'nullable|boolean',
            'right_to_work_status' => 'nullable|string',
            'right_to_work_certificate' => 'nullable|file|mimes:docx,doc,pdf,png,jpg',
            'share_code' => 'nullable|string',
            'date_issued' => 'nullable|date|date_format:Y-m-d',
            'date_checked' => 'nullable|date|date_format:Y-m-d',
            'expiry_date' => 'nullable|date|date_format:Y-m-d',
            'visa_required' => 'nullable|boolean',
            'visa_number' => 'nullable|string',
            'visa_start_date' => 'nullable|date|date_format:Y-m-d',
            'visa_expiry_date' => 'nullable|date|date_format:Y-m-d',
            'restrictions' => 'nullable|string',
            'is_dbs_required' => 'nullable|boolean',
            'self_declaration_completed' => 'nullable|boolean',
            'self_declaration_certificate' => 'nullable|file|mimes:docx,doc,pdf,png,jpg',
            'is_dbs_conducted' => 'nullable|boolean',
            'dbs_conducted_date' => 'nullable|date|date_format:Y-m-d',
            'follow_up_date' => 'nullable|date|date_format:Y-m-d',
            'dbs_certificate' => 'nullable|file|mimes:png,jpg,doc,docx,pdf',
            'dbs_certificate_number' => 'nullable|string',
            'driving_license_number' => 'nullable|string',
            'driving_license_country_of_issue' => 'nullable|string',
            'driving_license_class' => 'nullable|string',
            'driving_license_date_of_expiry' => 'nullable|date|date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'passport_number.required' => 'The passport_number field is required.',
            'passport_country_of_issue.required' => 'The passport_country_of_issue is required.',
            'passport_date_of_expiry.required' => 'The passport_date_of_expiry is required.',
            'is_uk_citizen.required' => 'The is_uk_citizen field is required.',
            'right_to_work_status.required' => 'The right_to_work_status field is required.',
            'share_code' => 'The share_code field is required.',
            'date_issued' => 'The date_issued field is required.',
            'date_checked' => 'The date_checked field is required.',
            'expiry_date' => 'The expiry_date field is required.',
            'visa_required' => 'The visa_required field is required',
            'visa_start_date.date' => 'The visa_start_date should be a valid date.',
            'visa_start_date.date_format' => 'The visa_start_date should be in Y-m-d format.',
            'visa_expiry_date.date' => 'The visa_expiry_date should be a valid date',
            'visa_expiry_date.date_format' => 'The visa_expiry_date should be in Y-m-d format',
            'dbs_conducted_date.date' => 'The dbs_conducted_date should be a valid date.',
            'dbs_conducted_date.date' => 'The dbs_conducted_date should be in Y-m-d.',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}