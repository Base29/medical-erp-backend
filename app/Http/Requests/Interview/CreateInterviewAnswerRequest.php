<?php

namespace App\Http\Requests\Interview;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateInterviewAnswerRequest extends FormRequest
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
            'interview' => 'required|numeric|exists:interview_schedules,id',
            'question' => 'required|numeric|exists:interview_questions,id',
            'answer' => 'nullable|string',
            'option' => 'nullable|numeric',
            'options' => 'nullable|array',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}