<?php

namespace App\Http\Requests\Offer;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateOfferRequest extends FormRequest
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
            'hiring_request' => 'required|numeric|exists:hiring_requests,id',
            'user' => 'required|numeric|exists:users,id',
            'work_pattern' => 'required|numeric|exists:work_patterns,id',
            'status' => [
                'nullable',
                Rule::in([
                    0, // Rejected/Declined
                    1, // Accepted
                    2, // Made
                    3, // Revised/Amended
                ]),
            ],
            'amount' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
        ];
    }

    public function messages()
    {
        return [
            'status.in' => 'The :attribute is invalid. :attribute can only be 0 => declined | 1 => accepted | 2 => made | 3 => revised',
            'amount.regex' => 'The amount should be in decimal 0.00',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}