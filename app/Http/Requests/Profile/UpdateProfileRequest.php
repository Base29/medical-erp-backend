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
            'profile' => 'nullable|numeric|exists:profiles,id',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'middle_name' => 'nullable|string',
            'maiden_name' => 'nullable|string',
            'profile_image' => 'nullable|file|mimes:png,jpg',
            'gender' => 'nullable|string',
            'professional_email' => 'nullable|email|unique:profiles,professional_email',
            'work_phone' => 'nullable|string',
            'home_phone' => 'nullable|string',
            'mobile_phone' => 'nullable|string',
            'dob' => 'nullable|date|date_format:Y-m-d',
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city' => 'nullable|string',
            'county' => 'nullable|string',
            'country' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'nhs_card' => 'nullable|string',
            'nhs_number' => 'nullable|string',
            'nhs_employment' => 'nullable|boolean',
            'nhs_smart_card_number' => 'nullable|string',
            'tutorial_completed' => 'nullable|boolean',
        ];
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}