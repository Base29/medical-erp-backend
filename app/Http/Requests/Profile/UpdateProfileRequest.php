<?php

namespace App\Http\Requests\Profile;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateProfileRequest extends FormRequest
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
            'profile' => 'required|numeric|exists:profiles,id',
            'first_name' => 'string',
            'last_name' => 'string',
            'middle_name' => 'string',
            'maiden_name' => 'string',
            'profile_image' => 'nullable|file|mimes:png,jpg',
            'gender' => 'string',
            'professional_email' => 'nullable|email|unique:profiles,professional_email',
            'work_phone' => 'string',
            'home_phone' => 'string',
            'mobile_phone' => 'string',
            'dob' => 'date|date_format:Y-m-d',
            'address_line_1' => 'string',
            'address_line_2' => 'string',
            'city' => 'string',
            'county' => 'string',
            'country' => 'string',
            'zip_code' => 'string',
            'nhs_card' => 'string',
            'nhs_number' => 'string',
        ];
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}