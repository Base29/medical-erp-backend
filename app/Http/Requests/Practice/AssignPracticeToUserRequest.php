<?php

namespace App\Http\Requests\Practice;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AssignPracticeToUserRequest extends FormRequest
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
            'email' => 'required|email|exists:users,email',
            'practice' => 'required|numeric|exists:practices,id',
            'type' => [
                'nullable',
                Rule::in(['user', 'practice-manager']),
            ],
        ];
    }

    public function messages()
    {
        return [
            'type.in' => 'The :attribute is invalid. It should be one of user|practice-manager',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}