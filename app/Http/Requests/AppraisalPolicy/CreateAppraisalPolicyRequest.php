<?php

namespace App\Http\Requests\AppraisalPolicy;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateAppraisalPolicyRequest extends FormRequest
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
            'practice' => 'nullable|numeric|exists:practices,id',
            'role' => 'required|numeric|exists:roles,id',
            'name' => 'required|string|max:100',
            'questions' => 'required|array',
            'questions.*.type' => [
                'required',
                Rule::in(['multi-choice', 'single-choice', 'descriptive']),
            ],
            'questions.*.question' => 'required|string|max:1000',
            'questions.*.options' => 'required_if:questions.*.type,multi-choice,single-choice|array',
            'questions.*.options.*.option' => 'required|string|max:1000',
            'questions.*.head' => [
                'required',
                'numeric',
                Rule::in([1, 2, 3, 4]),
            ],
        ];
    }

    public function messages()
    {
        return [
            'questions.required' => 'questions array is required.',
            'questions.*.question.required' => 'question field is required.',
            'questions.*.options.required' => 'The :attribute array is required.',
            'questions.*.type.in' => 'The :attribute is invalid. It should be one of multi-choice|single-choice|descriptive',
            'questions.*.head.in' => 'The :attribute is invalid. It should range from 1 to 4',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}