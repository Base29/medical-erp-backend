<?php

namespace App\Http\Requests\HeadQuarter;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SearchHiringRequest extends FormRequest
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
            'field' => [
                'required',
                Rule::in(['application_manager', 'is_live', 'job_title', 'status', 'progress', 'contract_type']),
            ],
            'search_term' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'field.in' => 'You can only filter results by these fields application_manager|is_live|job_title|status|progress|contract_type',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}