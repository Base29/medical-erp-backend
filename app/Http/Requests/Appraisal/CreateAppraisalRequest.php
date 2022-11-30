<?php

namespace App\Http\Requests\Appraisal;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateAppraisalRequest extends FormRequest
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
            'user' => 'required|numeric|exists:users,id',
            'department' => 'required|numeric|exists:departments,id',
            'date' => 'required|date|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'location' => 'nullable|string|max:500',
            'type' => [
                'required',
                Rule::in(['new', 'follow-up']),
            ],
            'status' => [
                'nullable',
                Rule::in(['first', 'second', 'final']),
            ],
            'additional_staff' => 'nullable|numeric|exists:users,id',
            'hq_staff' => 'nullable|numeric|exists:users,id',
            'duration' => 'nullable|string|max:50',
        ];
    }

    public function messages()
    {
        return [
            'status.in' => 'The status is invalid. It should be one of first|second|final',
            'type.in' => 'The type is invalid. It should be one of new|follow-up',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}