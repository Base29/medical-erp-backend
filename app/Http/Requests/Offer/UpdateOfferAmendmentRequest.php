<?php

namespace App\Http\Requests\Offer;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateOfferAmendmentRequest extends FormRequest
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
            'amendment' => 'required|numeric|exists:offer_amendments,id',
            'status' => [
                'required',
                Rule::in([
                    0, // Rejected/Declined
                    1, // Accepted
                ]),
            ],
            'reason' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'status.in' => 'The :attribute is invalid. :attribute can only be 0 => declined | 1 => accepted',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}