<?php

namespace App\Http\Requests\Interview;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateInterviewScoreRequest extends FormRequest
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
            'cultural_fit' => 'required|integer|between:0,10',
            'career_motivation' => 'required|integer|between:0,10',
            'social_skills' => 'required|integer|between:0,10',
            'team_work' => 'required|integer|between:0,10',
            'technical_skills' => 'required|integer|between:0,10',
            'leadership_capability' => 'required|integer|between:0,10',
            'critical_thinking_problem_solving' => 'required|integer|between:0,10',
            'self_awareness' => 'required|integer|between:0,10',
            'total' => 'required|integer|between:0,80',
            'remarks' => 'nullable|string|max:2000',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}