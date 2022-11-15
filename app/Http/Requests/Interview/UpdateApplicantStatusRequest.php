<?php

namespace App\Http\Requests\Interview;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateApplicantStatusRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'interview' => 'required|numeric|exists:interview_schedules,id',
            'applicant_status' => [
                'required',
                Rule::in([
                    0, // Rejected
                    1, // Accepted
                    2, // Referred for 2nd Interview
                ]),
            ],
        ];
    }

    public function messages()
    {
        return [
            'applicant_status.in' => 'The applicant_status can be 0 => Rejected | 1 => Accepted | 2 => Referred for 2nd Interview',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}