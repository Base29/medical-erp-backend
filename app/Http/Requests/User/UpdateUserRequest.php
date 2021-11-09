<?php

namespace App\Http\Requests\User;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateUserRequest extends FormRequest
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
            'first_name' => 'string',
            'last_name' => 'string',
            'profile_image' => 'nullable|file|mimes:png,jpg',
            'gender' => 'required',
            'email_professional' => 'nullable|email|unique:users,email_professional',
            'mobile_phone' => 'required|string',
            'dob' => 'date|date_format:Y-m-d',
            'address' => 'string',
            'city' => 'string',
            'county' => 'string',
            'country' => 'string',
            'zip_code' => 'string',
        ];
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}