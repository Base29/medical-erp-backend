<?php

namespace App\Http\Requests\HiringRequest;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateHiringRequest extends FormRequest
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
            'practice' => 'required|numeric|exists:practices,id',
            'job_title' => 'required|string|max:100',
            'contract_type' => 'required|string|max:50',
            'department' => 'required|numeric|exists:departments,id',
            'reporting_to' => 'required|numeric|exists:users,id',
            'start_date' => 'required|date|date_format:Y-m-d',
            'starting_salary' => 'required|string|max:50',
            'reason_for_recruitment' => 'required|string|max:60',
            'comment' => 'nullable|string|max:2000',
            'job_specification' => 'required|numeric|exists:job_specifications,id',
            'person_specification' => 'required|numeric|exists:person_specifications,id',
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