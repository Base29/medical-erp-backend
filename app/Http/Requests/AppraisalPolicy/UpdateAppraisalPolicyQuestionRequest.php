<?php

namespace App\Http\Requests\AppraisalPolicy;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateAppraisalPolicyQuestionRequest extends FormRequest
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
            'question_id' => 'required|numeric|exists:interview_questions,id',
            'type' => [
                'nullable',
                Rule::in(['multi-choice', 'single-choice', 'descriptive']),
            ],
            'question' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'question_id.required' => 'question id is required.',
            'type.in' => 'The :attribute is invalid. It should be one of multi-choice|single-choice|descriptive',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}