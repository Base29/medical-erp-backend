<?php

namespace App\Http\Requests\Permission;

use App\Helpers\CustomValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreatePermissionRequest extends FormRequest
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
            'name' => 'required|unique:permissions,name',
        ];
    }

    public function messages()
    {
        return [
            'unique' => 'Permission ' . request()->name . ' already exist',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidation::error_messages($this->rules(), $validator));
    }
}