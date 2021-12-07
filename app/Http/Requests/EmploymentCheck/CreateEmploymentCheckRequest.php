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
            'passport_number' => 'required|string',
            'passport_country_of_issue' => 'required|string',
            'passport_date_of_expiry' => 'required|date|date_format:Y-m-d',
            'is_uk_citizen' => 'required|boolean',
            'right_to_work_status' => 'required|string',
            'share_code' => 'required|string',
            'date_issued' => 'required|date|date_format:Y-m-d',
            'date_checked' => 'required|date|date_format:Y-m-d',
            'expiry_date' => 'required|date|date_format:Y-m-d',
            'visa_required' => 'required|boolean',
            'visa_number',
            'visa_start_date',
            'visa_expiry_date',
            'restrictions',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}