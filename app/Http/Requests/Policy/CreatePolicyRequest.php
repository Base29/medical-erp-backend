<?php

namespace App\Http\Requests\Policy;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreatePolicyRequest extends FormRequest
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
            'name' => 'required|unique:policies,name',
            'attachment' => 'required|file|mimes:doc,docx,pdf',
            'practice' => 'required|numeric|exists:practices,id',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'Policy ' . request()->name . ' already exists',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}