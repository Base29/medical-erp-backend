<?php

namespace App\Http\Requests\HiringRequest;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SearchVacanciesRequest extends FormRequest
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
            'filter' => [
                'required',
                Rule::in([
                    'role',
                    'location',
                    'manager',
                    'department',
                    'job_specification',
                    'person_specification',
                    'contract_type',
                    'reporting_to',
                    'status',
                    'progress',
                    'is_live',
                ]),
            ],
            'value' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'filter.in' => 'Filter "' . request()->filter . '" is not allowed. Only role|location|manager filters are allowed',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}