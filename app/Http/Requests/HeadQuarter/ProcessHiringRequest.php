<?php

namespace App\Http\Requests\HeadQuarter;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProcessHiringRequest extends FormRequest
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
            'status' => [
                'required',
                Rule::in(['approved', 'declined', 'escalated', 'hired']),
            ],
            'decision_reason' => 'nullable|string|max:100',
            'decision_comment' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'status.in' => 'Select either of the status APPROVED|ESCALATED|DECLINED|HIRED',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}