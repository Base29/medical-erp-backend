<?php

namespace App\Http\Requests\Offer;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateOfferRequest extends FormRequest
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
            'offer' => 'required|numeric|exists:offers,id',
            'status' => [
                'nullable',
                Rule::in([
                    0, // Rejected/Declined
                    1, // Accepted
                    2, // Made
                    3, // Revised/Amended
                    4, // Pending
                    5, // Discarded
                ]),
            ],
            'amount' => 'nullable|string|max:10',
            'work_pattern_id' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'reason' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'status.in' => 'The :attribute is invalid. :attribute can only be 0 => declined | 1 => accepted | 2 => made | 3 => revised',
            'amount.regex' => 'The amount should be in decimal 0.00',
        ];
    }

    public function validated($key = null, $default = null)
    {
        if (request()->has('status')):
            if (request()->status === 5):
                return array_merge(parent::validated(), [
                    'is_active' => 0,
                ]);
            else:
                return parent::validated($key, $default);
            endif;
        endif;
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}