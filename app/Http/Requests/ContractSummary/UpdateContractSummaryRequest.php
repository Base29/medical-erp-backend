<?php

namespace App\Http\Requests\ContractSummary;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateContractSummaryRequest extends FormRequest
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
            'contract_summary' => 'required|numeric|exists:contract_summaries,id',
            'employee_type' => 'nullable|string',
            'employee_start_date' => 'nullable|date|date_format:Y-m-d H:i:s',
            'contract_start_date' => 'nullable|date|date_format:Y-m-d H:i:s',
            'working_time_pattern' => 'nullable|string',
            'contracted_hours_per_week' => 'nullable|string',
            'contract_document' => 'nullable|file|mimes:doc,docx,pdf',
            'min_leave_entitlement' => 'nullable|string',
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