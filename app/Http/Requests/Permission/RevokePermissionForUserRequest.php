<?php

namespace App\Http\Requests\Permission;

use App\Helpers\CustomValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RevokePermissionForUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
            'permission' => 'required|exists:permissions,name',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidation::error_messages($this->rules(), $validator));
    }
}