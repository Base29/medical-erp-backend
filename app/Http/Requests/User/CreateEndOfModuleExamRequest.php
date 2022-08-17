<?php

namespace App\Http\Requests\User;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateEndOfModuleExamRequest extends FormRequest
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
            'module' => 'required|numeric|exists:course_modules,id',
            'user' => 'required|numeric|exists:users,id',
            'type' => 'required|string',
            'number_of_questions' => 'required|numeric',
            'is_restricted',
            'duration',
            'description',
            'url',
            'is_passing_percentage',
            'passing_percentage',
            'is_passed',
            'grade_achieved',
            'percentage_achieved',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}