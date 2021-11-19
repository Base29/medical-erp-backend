<?php

namespace App\Http\Requests\ContractSummary;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class FetchSingleContractSummaryRequest extends FormRequest
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