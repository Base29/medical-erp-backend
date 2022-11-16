<?php

namespace App\Http\Requests\Offer;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OfferAmendmentRequest extends FormRequest
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
            'offer' => 'required|numeric|exists:offers,id',
            'hiring_request' => 'nullable|numeric|exists:hiring_requests,id',
            'work_pattern' => 'nullable|numeric|exists:work_patterns,id',
            'user' => 'nullable|numeric|exists:users,id',
            'amount' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
        ];
    }

    public function messages()
    {
        return [
            'amount.regex' => 'The amount should be in decimal 0.00',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}