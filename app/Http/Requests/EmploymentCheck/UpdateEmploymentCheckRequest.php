<?php

namespace App\Http\Requests\EmploymentCheck;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateEmploymentCheckRequest extends FormRequest
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
            'employment_check' => 'required|numeric|exists:employment_checks,id',
            'passport_number' => 'nullable|string',
            'passport_country_of_issue' => 'nullable|string',
            'passport_date_of_expiry' => 'nullable|date|date_format:Y-m-d',
            'is_uk_citizen' => 'nullable|boolean',
            'right_to_work_status' => 'nullable|string',
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
            'driving_license_number' => 'nullable|string',
            'driving_license_country_of_issue' => 'nullable|string',
            'driving_license_class' => 'nullable|string',
            'driving_license_date_of_expiry' => 'nullable|date|date_format:Y-m-d',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}