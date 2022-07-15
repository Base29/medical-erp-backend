<?php

namespace App\Http\Requests\InductionResult;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateInductionResultSingleRequest extends FormRequest
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
            'induction_schedule' => 'required|numeric|exists:induction_schedules,id',
            'induction_checklist' => 'required|numeric|exists:induction_checklists,id',
            'question' => 'required|numeric|exists:induction_questions,id',
            'completed' => 'required|boolean',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}