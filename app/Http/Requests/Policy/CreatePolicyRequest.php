<?php

namespace App\Http\Requests\Policy;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
            'name' => 'nullable|unique:policies,name',
            'description' => 'nullable|string|max:2000',
            'type' => [
                'nullable',
                'string',
                Rule::in(['clinical-governance', 'health-and-safety', 'hr-and-training', 'admin']),
            ],
            'attachment' => 'required|file|mimes:doc,docx,pdf',
            'practice' => 'nullable|numeric|exists:practices,id',
            'roles' => 'nullable|array',
            'roles.*.role' => 'required_with:roles|numeric|exists:roles,id',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'Policy ' . request()->name . ' already exists',
            'type.in' => 'Policy type should be one of clinical-governance|health-and-safety|hr-and-training|admin',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}