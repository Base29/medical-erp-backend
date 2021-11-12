<?php

namespace App\Http\Requests\User;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateUserRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'profile_image' => 'nullable|file|mimes:png,jpg',
            'password' => 'required|confirmed',
            'gender' => 'required',
            'email_professional' => 'nullable|email|unique:users,email_professional',
            'mobile_phone' => 'required|string',
            'dob' => 'required|date|date_format:Y-m-d',
            'address' => 'required|string',
            'city' => 'required|string',
            'county' => 'required|string',
            'country' => 'required|string',
            'zip_code' => 'required|string',
            'additional_roles' => 'nullable|array',
            'job_title' => 'required|string',
            'contract_type' => 'required|string',
            'department' => 'required|string',
            'reports_to' => 'required|string',
            'probation_end_date' => 'required|date|date_format:Y-m-d',
            'notice_period' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'email_professional.unique' => 'Professional email ' . request()->email_professional . ' already associated with another account',
            'primary_role.required' => 'User should have a primary role.',
        ];
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }

}