<?php

namespace App\Http\Requests\User;

use App\Helpers\CustomValidation;
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
            'profile_image' => 'required|file|mimes:png,jpg',
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
        ];
    }

    public function messages()
    {
        return [
            'email_professional.unique' => 'Professional email ' . request()->email_professional . ' already associated with another account',
        ];
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidation::error_messages($this->rules(), $validator));
    }

}