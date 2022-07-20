<?php

namespace App\Http\Requests\Appraisal;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateAppraisalRequest extends FormRequest
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
            'appraisal' => 'required|numeric|exists:appraisals,id',
            'is_completed' => 'nullable|boolean',
            'progress' => 'nullable|numeric',
            'is_rescheduled' => 'nullable|boolean',
            'reschedule_reason' => 'nullable|string|max:500',
            'appraisal_reference' => 'required_if:is_rescheduled,1|numeric|exists:appraisals,id',
        ];
    }

    public function messages()
    {
        return [
            'appraisal_reference.required_if' => 'The appraisal_reference field is required when is_rescheduled = 1',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}