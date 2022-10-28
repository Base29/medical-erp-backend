<?php

namespace App\Http\Requests\HiringRequest;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FetchHiringRequest extends FormRequest
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
            'status' => [
                'nullable',
                Rule::in(['pending', 'approved', 'declined', 'escalated', 'hired']),
            ],
            'role' => 'nullable|numeric|exists:roles,id',
            'progress' => [
                'nullable',
                Rule::in(['pending-approval', 'in-process', 'interviews-in-progress', 'offer-made', 'completed']),
            ],
            'job_title' => 'nullable|string',
            'contract_type' => [
                'nullable',
                Rule::in(['permanent', 'casual', 'fixed-term', 'zero-hour']),
            ],
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}