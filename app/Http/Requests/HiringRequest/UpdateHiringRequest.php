<?php

namespace App\Http\Requests\HiringRequest;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateHiringRequest extends FormRequest
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
            'hiring_request' => 'required|numeric|exists:hiring_requests,id',
            'job_title' => 'nullable|string|max:100',
            'contract_type' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:50',
            'reporting_to' => 'nullable|numeric|exists:roles,id',
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'starting_salary' => 'nullable|string|max:50',
            'reason_for_recruitment' => 'nullable|string|max:60',
            'comment' => 'nullable|string|max:2000',
            'rota_information' => 'nullable|numeric',
            'name' => 'nullable|string|unique:work_patterns,name',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'break_time' => 'nullable|numeric',
            'repeat_days' => 'nullable|array',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}