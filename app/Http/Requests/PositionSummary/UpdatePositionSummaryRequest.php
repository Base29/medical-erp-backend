<?php

namespace App\Http\Requests\PositionSummary;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdatePositionSummaryRequest extends FormRequest
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
            'position_summary' => 'required|numeric|exists:position_summaries,id',
            'job_title' => 'nullable|string',
            'contract_type' => 'nullable|string',
            'department' => 'nullable|string',
            'reports_to' => 'nullable|string',
            'probation_end_date' => 'nullable|date|date_format:Y-m-d',
            'notice_period' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'position_summary.required' => 'The position_summary field is required.',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException(
            $validator,
            CustomValidationService::error_messages($this->rules(),
                $validator
            )
        );
    }
}