<?php

namespace App\Http\Requests\PositionSummary;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreatePositionSummaryRequest extends FormRequest
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
            'user' => 'required|numeric|exists:users,id|unique:position_summaries,user_id',
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
            'user.unique' => 'This user already has a position summary',
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