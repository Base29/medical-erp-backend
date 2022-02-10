<?php

namespace App\Http\Requests\InterviewPolicy;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateInterviewPolicyRequest extends FormRequest
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
            'practice' => 'required|numeric|exists:practices,id',
            'role' => 'required|numeric|exists:roles,id',
            'name' => 'required|string|max:100',
            'questions' => 'required|array',
            'questions.*.type' => 'required|string',
            'questions.*.question' => 'required|string|max:1000',
            'questions.*.options' => 'required_if:questions.*.type,multi-choice,single-choice|array',
            'questions.*.options.*.option' => 'required|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'questions.required' => 'questions array is required.',
            'questions.*.question.required' => 'question field is required.',
            'questions.*.options.required' => 'The :attribute array is required.',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}