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
            'password' => 'nullable|confirmed',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'is_candidate' => 'required|boolean',
            'gender' => 'nullable|string',
            'mobile_phone' => 'nullable|string',
            'additional_roles' => 'nullable|array',
            'job_title' => 'nullable|string|exists:roles,name',
            'contract_type' => 'nullable|string',
            'contract_start_date' => 'nullable|date|date_format:Y-m-d',
            'contracted_hours_per_week' => 'nullable|string',
            'additional_roles' => 'nullable|array',
            'hiring_request' => 'required_if:is_candidate,1|exists:hiring_requests,id',
        ];
    }

    public function messages()
    {
        return [
            'primary_role.required' => 'User should have a primary role.',
            'is_candidate.required' => 'The is_candidate field is required',
        ];
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }

}