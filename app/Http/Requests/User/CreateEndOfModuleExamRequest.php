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
            'type' => 'required|string',
            'number_of_questions' => 'required|numeric',
            'is_restricted' => 'nullable|boolean',
            'duration' => 'nullable|string',
            'description' => 'nullable|string|max:2000',
            'url' => 'nullable|string',
            'is_passing_percentage' => 'nullable|boolean',
            'passing_percentage' => 'required_if:is_passing_percentage,1|numeric',
            'is_passed' => 'nullable|boolean',
            'grade_achieved' => 'required_if:is_passed,1|string',
            'percentage_achieved' => 'required_if:is_passed,1|numeric',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}