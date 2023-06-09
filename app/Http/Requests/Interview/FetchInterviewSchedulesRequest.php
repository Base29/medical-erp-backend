<?php

namespace App\Http\Requests\Interview;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FetchInterviewSchedulesRequest extends FormRequest
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
            'practice' => 'nullable|numeric|exists:practices,id',
            'application_status' => [
                'nullable',
                Rule::in(['first-interview', 'second-interview']),
            ],
            'department' => 'nullable|numeric|exists:departments,id',
            'interview_type' => [
                'nullable',
                Rule::in(['digital-interview', 'physical-interview']),
            ],
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}